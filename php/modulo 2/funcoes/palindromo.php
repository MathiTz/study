<div class="titulo">Palindromo</div>

<?php

//function isPalindrome($str) {
//  $toLowerStr = strtolower($str);
//
//  $revStr = strrev($toLowerStr);
//
//  return $revStr === $toLowerStr;
//}

function isPalindrome($str) {
  $lastIndex = strlen($str) - 1;

  for ($i = 0; $i <= $lastIndex; $i++) {
    if ($str[$i] !== $str[$lastIndex - $i]) {
      return false;
    }
  }

  return true;
}

$str1 = "ana";
$str2 = "banana";
$str3 = "bola";

echo '<br>', "{$str1} ", isPalindrome($str1) ? 'is' : "isn't", " a palindrome";
echo '<br>', "{$str2} ", isPalindrome($str2) ? 'is' : "isn't", " a palindrome";
echo '<br>', "{$str3} ", isPalindrome($str3) ? 'is' : "isn't", " a palindrome";
