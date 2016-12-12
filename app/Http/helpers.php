<?php

//namespace App\Http;

/**
 * Converts numeric $row/$col notation to an Excel cell reference string in
 * A1 notation.
 *
 * Borrowed from: https://github.com/thoroc/php_writeexcel/blob/master/src/functions.writeexcel_utility.inc.php#L27
 */
function xl_rowcol_to_cell($row, $col, $row_abs = false, $col_abs = false)
{
    $row_abs = $row_abs ? '$' : '';
    $col_abs = $col_abs ? '$' : '';
    $int  = floor($col / 26);
    $frac = $col % 26;
    $chr1 = ''; // Most significant character in AA1
    if ($int > 0) {
        $chr1 = chr(ord('A') + $int - 1);
    }
    $chr2 = chr(ord('A') + $frac);
    // Zero index to 1-index
    ++$row;
    return $col_abs.$chr1.$chr2.$row_abs.$row;
}
