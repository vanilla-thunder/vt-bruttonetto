<?php

/**
 * vt both prices
 * The MIT License (MIT)
 *
 * Copyright (C) 2015  Marat Bedoev
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * Author:     Marat Bedoev <m@marat.ws>
 */

class vt_bruttonetto_oxutilsview extends vt_bruttonetto_oxutilsview_parent
{

public function getSmarty($blReload = false)
{
    $smarty = parent::getSmarty($blReload);
    $smarty->unregister_function( 'oxprice' );
    $smarty->register_function( 'oxprice',  array( $this, 'smarty_function_vt_oxprice' ) );

    return $smarty;
}

function smarty_function_vt_oxprice( $params, &$smarty )
{
    $sOutput = '';
    $iDecimals = 2;
    $sDecimalsSeparator = ',';
    $sThousandSeparator = '.';
    $sCurrencySign = '';
    $sSide = '';
    $mPrice = $params['price'];

    if ( !is_null( $mPrice ) ) {

        $oCurrency = isset( $params['currency'] ) ? $params['currency'] : null;

        if ( !is_null( $oCurrency ) ) {
            $sDecimalsSeparator = isset( $oCurrency->dec ) ? $oCurrency->dec : $sDecimalsSeparator;
            $sThousandSeparator = isset( $oCurrency->thousand ) ? $oCurrency->thousand : $sThousandSeparator;
            $sCurrencySign = isset( $oCurrency->sign ) ? $oCurrency->sign : $sCurrencySign;
            $sSide = isset( $oCurrency->side ) ? $oCurrency->side : $sSide;
            $iDecimals = isset( $oCurrency->decimal ) ? (int) $oCurrency->decimal : $iDecimals;
        }

        if( $mPrice instanceof oxPrice )
        {
            $sBruttoPrice = $mPrice->getBruttoPrice();
            if ( is_numeric( $sBruttoPrice ) ) {
                if ( (float) $sBruttoPrice > 0 || $sCurrencySign  ) {
                    $sBruttoPrice = number_format( $sBruttoPrice, $iDecimals, $sDecimalsSeparator, $sThousandSeparator );
                    $sBruttoOutput = ( isset($sSide) && $sSide == 'Front' ) ? $sCurrencySign . $sBruttoPrice : $sBruttoPrice . ' ' . $sCurrencySign;
                }

                $sOutput = trim($sBruttoOutput);
            }
            
            $sNettoPrice  = $mPrice->getNettoPrice();
            if ( is_numeric( $sNettoPrice ) ) {
                if ( (float) $sNettoPrice > 0 || $sCurrencySign  ) {
                    $sNettoPrice = number_format( $sNettoPrice, $iDecimals, $sDecimalsSeparator, $sThousandSeparator );
                    $sNettoOutput = ( isset($sSide) && $sSide == 'Front' ) ? $sCurrencySign . $sNettoPrice : $sNettoPrice . ' ' . $sCurrencySign;
                }

                $sOutput .= ' <br/><small>('.trim($sNettoOutput) . ' netto)</small>';
            }
            
        }
        else 
        {
            // default behaviour
            $sPrice = $mPrice;
            if ( is_numeric( $sPrice ) ) {
                if ( (float) $sPrice > 0 || $sCurrencySign  ) {
                    $sPrice = number_format( $sPrice, $iDecimals, $sDecimalsSeparator, $sThousandSeparator );
                    $sOutput = ( isset($sSide) && $sSide == 'Front' ) ? $sCurrencySign . $sPrice : $sPrice . ' ' . $sCurrencySign;
                }

                $sOutput = trim($sOutput);
            }
        }
    }

    return $sOutput;
}
}
