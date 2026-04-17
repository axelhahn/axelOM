<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * ADMIN ABOUT
 * 
 */


$sContextbar='';
$TITLE=icon::get('about') . '{{about.title}}';
$BANNER='{{home.banner}}';

$BODY=$renderAdminLTE->addRow(

    $renderAdminLTE->addCol(
        $renderAdminLTE->getCard([
            'type' => '',
            'title' => '{{about.text.title}}',
            'text' => '{{about.text.content}}'
                . '
                    <br>
                    <a href="https://github.com/axelhahn/axelOM" target="_blank" class="btn btn-outline-success btn-big">'.icon::get('code').'<br>{{about.source}}</a>

                    <!--
                    <a href="https://www.axel-hahn/docs/axelom" target="_blank" class="btn btn-outline-dark btn-big">'.icon::get('help').'<br>{{about.docs}}</a>
                    -->
                    <a href="https://github.com/axelhahn/axelOM/tree/main/docs" target="_blank" class="btn btn-outline-dark btn-big">'.icon::get('help').'<br>{{about.docs}}</a>
                '

            ,
            // 'variant' => '',
        ]), 
        6
    )

    .$renderAdminLTE->addCol(
        $renderAdminLTE->getCard([
            'type' => '',
            'title' => '{{about.thanks.title}}',
            'text' => '{{about.thanks.content}}'
            ,
            // 'variant' => '',
        ]), 
        6
        )

);
