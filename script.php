<?php

error_reporting (E_ALL & ~(defined("E_DEPRECATED") ? E_DEPRECATED : 0) & ~E_STRICT);

// require_once 'core/lib/core.lib.php';
// require_once 'core/lib/csv_log.lib.php';


/*
  Some inlined library functions
*/

function vd($v, $l = NULL)
{
  if (isset($l))
    echo $l . ': ';
  // var_dump($v);
  ob_start();
  var_dump($v);
  $c = ob_get_clean();
  echo addcslashes($c, "\x00..\x08\x0B\x0C\x0E..\x1F");
  return $v;
}

function vdt($v = true, $l = NULL)
{
  if ($v === true)
    $v = time();
  vd(($v ? date('r', $v) : $v), $l);
  return $v;
}

function vdd($v, $l = NULL)
{
  vd($v, $l);
  die(1);
}

function verbose($s)
{
  echo $s;
  echo "\n";
}

function verbosef($formatStr, $p1 = NULL, $p2 = NULL, $p3 = NULL, $p4 = NULL, $p5 = NULL, $p6 = NULL, $p7 = NULL, $p8 = NULL, $p9 = NULL)
{
  return verbose(sprintf($formatStr, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9));
}



// https://en.wikipedia.org/wiki/Trie
class PrefixTree
{
  var $root = array();

  function contains($vector)
  {
    $node =& $this->root;

    foreach ($vector as $v)
      if (isset($node[$v]))
        $node =& $node[$v];
      else
        return false;

    // return $node.value;
    return true;
  }

  function add($vector)
  {
    $node =& $this->root;
    $i = 0;
    $n = count($vector);

    while ($i < $n) {
      if (isset($node[$vector[$i]])) {
        $node =& $node[$vector[$i]];
        $i++;
      } else {
        break;
      }
    }

    // append new nodes, if necessary
    while ($i < $n) {
      $node[$vector[$i]] = array();  // new node
      $node =& $node[$vector[$i]];
      $i++;
    }

    // $node.value = $value;
  }
}


class Application
{
  function prefixTreeReduce($node, $level = 0)
  {
    if (!isset($node) || count($node) == 0)
      return '';

    $v = '';
    $firstChild = true;
    foreach ($node as $childLabel => $childNode) {
      // $v .= ($firstChild ? '' : '|') . ($level > 0 ? '\.' : '') . $childLabel . $this->prefixTreeReduce($childNode, $level + 1);
      $v .= ($firstChild ? '' : '|') . $childLabel . ($level < 3 ? '\.' : '') . $this->prefixTreeReduce($childNode, $level + 1);
      $firstChild = false;
    }
    if (count($node) > 1)
      $v = '(' . $v . ')';

    return $v;
  }

  function execute($actionName = NULL)
  {
    $fileName = $GLOBALS['argv'][1];

    $ipList = file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Sort by octets
    $ipListMap = array();
    foreach ($ipList as $ip) {
      $k = $ip;
      $k = preg_replace("@(?<=^|\.)(\d{2})(?=\.|$)@", "0$1", $k);
      $k = preg_replace("@(?<=^|\.)(\d{1})(?=\.|$)@", "00$1", $k);
      if (!preg_match("@^\d{3}\.\d{3}\.\d{3}\.\d{3}$@", $k))
        trigger_error("Invalid IPv4 address: {$ip}", E_USER_ERROR);
      // vd($k, $ip);
      $ipListMap[$k] = $ip;
    }
    ksort($ipListMap);
    $ipList = array_values($ipListMap);
    // vd($ipList);

    // $ipList = array_slice($ipList, 0, 20);

    $tree = new PrefixTree();
    foreach ($ipList as $ip) {
      $vector = explode('.', $ip);
      $tree->add($vector);
    }

    $re = '^' . $this->prefixTreeReduce($tree->root) . '$';
    vd($re, 're');

    // Test matching
    $matchCount = 0;
    // for ($i = 0; $i < 1000; $i++) $ipList[] = mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255);
    $t0 = microtime(true);
    foreach ($ipList as $ip) {
      if (preg_match("@{$re}@", $ip)) {
        $matchCount++;
      } else {
        // vd($ip, 'unmatched');
      }
    }
    $t = microtime(true) - $t0;
    verbosef("%d of %d IPs matched, %d of %d unmatched, %d ms, %d iterations/ms", $matchCount, count($ipList), count($ipList) - $matchCount, count($ipList), $t * 1000, count($ipList) / ($t * 1000));
    file_put_contents($GLOBALS['argv'][2], $re);
  }
}


$GLOBALS['CORE_CONFIG']['core/command_line_execution'] = true;
$GLOBALS['CORE_CONFIG']['core/verbose'] = true;
$GLOBALS['CORE_CONFIG']['core/verbose/displayLevel'] = 2;
ini_set('pcre.recursion_limit', max(10000000, ini_get('pcre.recursion_limit')));
ini_set('pcre.backtrack_limit', max(10000000, ini_get('pcre.backtrack_limit')));
mb_internal_encoding("utf-8");

$application = new Application();
$application->execute();


?>
