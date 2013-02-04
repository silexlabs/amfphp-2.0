<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * top link bar
 * 
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice
 * 
 */

/**
 * dummy to get rid of phpdocumentor error...
 */
$temp = 0;
?>
        <div class='static'>
            <a href="?" id="titleLink"><b>AmfPHP Back Office<?php
if (isset($addToTitle)) {
    echo $addToTitle;
}
?></b></a>
            <ul>
                <li><a href="http://www.silexlabs.org/amfphp/feedback/">Feedback</a></li>
                <li><a href="http://www.silexlabs.org/groups/amfphp/amfphp-users/">Forum</a></li>
                <li><a href="http://www.silexlabs.org/amfphp/documentation/">Documentation</a></li>
                <li><a href="http://community.silexlabs.org/amfphp/reference/">Class Reference</a></li>
                <li><a href="https://github.com/silexlabs/amfphp-2.0">Source Code</a></li>
                <li><a href="http://www.silexlabs.org/amfphp/">AmfPHP Website</a></li>
                <li><a href="http://www.silexlabs.org/">Silex Labs</a></li>
                <li><a href="http://www.silexlabs.org/category/exchange/exchange-amfphp/">More Plugins</a></li>

            </ul>
        </div>