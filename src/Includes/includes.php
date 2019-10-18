<?php declare(strict_types=1);

/**
 * str_pad for multi-byte strings
 *
 * @param      [type]  $str       [description]
 * @param      [type]  $pad_len   [description]
 * @param      string  $pad_str   [description]
 * @param      [type]  $dir       [description]
 * @param      [type]  $encoding  [description]
 *
 * @return     string
 */
function mb_str_pad($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT, $encoding = NULL)
{
    $encoding = $encoding === NULL ? mb_internal_encoding() : $encoding;
    $padBefore = $dir === STR_PAD_BOTH || $dir === STR_PAD_LEFT;
    $padAfter = $dir === STR_PAD_BOTH || $dir === STR_PAD_RIGHT;
    $pad_len -= mb_strlen($str, $encoding);
    $targetLen = $padBefore && $padAfter ? $pad_len / 2 : $pad_len;
    $strToRepeatLen = mb_strlen($pad_str, $encoding);
    $repeatTimes = ceil($targetLen / $strToRepeatLen);
    $repeatedString = str_repeat($pad_str, (int) max(0, $repeatTimes)); // safe if used with valid utf-8 strings
    $before = $padBefore ? mb_substr($repeatedString, 0, (int) floor($targetLen), $encoding) : '';
    $after = $padAfter ? mb_substr($repeatedString, 0, (int) ceil($targetLen), $encoding) : '';

    return $before . $str . $after;
}

/**
 * Return $namespace prefixed with a / if it is given
 *
 * Removes any \ chars from the string
 *
 * @param      string  $namespace  [description]
 *
 * @return     string
 */
function namespaceFilename(string $namespace)
{
  if (!empty($namespace)) {
    $namespace = str_replace('\\','/',$namespace);
    $namespace = '/' . trim($namespace,'\\/');
  }
  return $namespace;
}

/**
 * Format Namespace with / in front
 *
 * @param      string  $namespace  The namespace
 */
function formatNamespace(string $namespace)
{
  if (!empty($namespace)) {
    $namespace = str_replace('\\','/',$namespace);
    $namespace = '/' . trim($namespace,'\\/');
  }

  return $namespace;
}
