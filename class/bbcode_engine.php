<?php
declare(strict_types=1);

/**
 * MCCodes Version 2.0.5b
 * Copyright (C) 2005-2012 Dabomstew
 * All rights reserved.
 *
 * Redistribution of this code in any form is prohibited, except in
 * the specific cases set out in the MCCodes Customer License.
 *
 * This code license may be used to run one (1) game.
 * A game is defined as the set of users and other game database data,
 * so you are permitted to create alternative clients for your game.
 *
 * If you did not obtain this code from MCCodes.com, you are in all likelihood
 * using it illegally. Please contact MCCodes to discuss licensing options
 * in this case.
 *
 * File: bbcode_engine.php
 * Signature: d34c12616ac9beca032b0eb80ba92a98
 * Date: Fri, 20 Apr 12 08:50:30 +0000
 * @noinspection SpellCheckingInspection
 */

class bbcode_engine
{
    private static ?self $instance = null;
    public array $parsings = [];
    public array $htmls = [];
    public array $parsings_with_callbacks = [];
    public array $htmls_with_callbacks = [];
    public static function getInstance(): ?self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param string $tag
     * @return void
     */
    public function simple_bbcode_tag(string $tag = ''): void
    {
        if (!$tag) {
            return;
        }
        $this->parsings[] = '/\[' . $tag . '\](.+?)\[\/' . $tag . '\]/';
        $this->htmls[]    = '<' . $tag . ">\\1</" . $tag . '>';
    }

    /**
     * @param string $tag
     * @param string $reptag
     * @return void
     */
    public function adv_bbcode_tag(string $tag = '', string $reptag = ''): void
    {
        if (!$tag) {
            return;
        }
        $this->parsings[] = '/\[' . $tag . '\](.+?)\[\/' . $tag . '\]/';
        $this->htmls[]    = '<' . $reptag . ">\\1</" . $reptag . '>';
    }

    /**
     * @param string $tag
     * @param string $optionval
     * @return void
     */
    public function simple_option_tag(string $tag = '', string $optionval = ''): void
    {
        if ($tag == '' || $optionval == '') {
            return;
        }
        $this->parsings[] =
            '/\[' . $tag . '=(.+?)\](.+?)\[\/' . $tag . '\]/';
        $this->htmls[]    =
            '<' . $tag . ' ' . $optionval . "='\\1'>\\2</" . $tag . '>';
    }

    /**
     * @param string $tag
     * @param string $reptag
     * @param string $optionval
     * @return void
     */
    public function adv_option_tag(string $tag = '', string $reptag = '', string $optionval = ''): void
    {

        if ($tag == '' || $optionval == '' || $reptag == '') {
            return;
        }
        $this->parsings[] =
            '/\[' . $tag . '=(.+?)\](.+?)\[\/' . $tag . '\]/';
        $this->htmls[]    =
            '<' . $reptag . ' ' . $optionval . "='\\1'>\\2</" . $reptag
            . '>';
    }

    /**
     * @param string $tag
     * @param string $reptag
     * @param string $optionval
     * @return void
     */
    public function adv_option_tag_em(string $tag = '', string $reptag = '', string $optionval = ''): void
    {

        if ($tag == '' || $optionval == '' || $reptag == '') {
            return;
        }
        $this->parsings[] =
            '/\[' . $tag . '=(.+?)\](.+?)\[\/' . $tag . '\]/';
        $this->htmls[]    =
            '<' . $reptag . ' ' . $optionval . "='mailto:\\1'>\\2</"
            . $reptag . '>';
    }

    /**
     * @param string $tag
     * @param string $optionval
     * @return void
     */
    public function simp_option_notext(string $tag = '', string $optionval = ''): void
    {

        if ($tag == '' || $optionval == '') {
            return;
        }
        $this->parsings[] = '/\[' . $tag . '=(.+?)\]/';
        $this->htmls[]    = '<' . $tag . ' ' . $optionval . "='\\1' />";
    }

    /**
     * @param string $tag
     * @param string $reptag
     * @param string $optionval
     * @return void
     */
    public function adv_option_notext(string $tag = '', string $reptag = '', string $optionval = ''): void
    {

        if ($tag == '' || $optionval == '' || $reptag == '') {
            return;
        }
        $this->parsings[] = '/\[' . $tag . '=(.+?)\]/';
        $this->htmls[]    = '<' . $reptag . ' ' . $optionval . "='\\1' />";
    }

    /**
     * @param string $tag
     * @param string $reptag
     * @param string $optionval
     * @return void
     */
    public function adv_option_notext_em(string $tag = '', string $reptag = '', string $optionval = ''): void
    {

        if ($tag == '' || $optionval == '' || $reptag == '') {
            return;
        }
        $this->parsings[] = '/\[' . $tag . '=(.+?)\]/';
        $this->htmls[]    =
            '<' . $reptag . ' ' . $optionval . "='mailto:\\1' >\\1</"
            . $reptag . '>';
    }

    /**
     * @param string $tag
     * @param string $optionval
     * @return void
     */
    public function simp_bbcode_att(string $tag = '', string $optionval = ''): void
    {

        if ($tag == '' || $optionval == '') {
            return;
        }
        $this->parsings[] = '/\[' . $tag . '\](.+?)\[\/' . $tag . '\]/';
        $this->htmls[]    = '<' . $tag . ' ' . $optionval . "='\\1' />";
    }

    /**
     * @param string $tag
     * @param string $reptag
     * @param string $optionval
     * @return void
     */
    public function adv_bbcode_att(string $tag = '', string $reptag = '', string $optionval = ''): void
    {

        if ($tag == '' || $optionval == '' || $reptag == '') {
            return;
        }
        $this->parsings[] = '/\[' . $tag . '\](.+?)\[\/' . $tag . '\]/';
        $this->htmls[]    = '<' . $reptag . ' ' . $optionval . "='\\1' />";
    }

    /**
     * @param string $tag
     * @param string $reptag
     * @param string $optionval
     * @return void
     */
    public function adv_bbcode_att_em(string $tag = '', string $reptag = '', string $optionval = ''): void
    {

        if ($tag == '' || $optionval == '' || $reptag == '') {
            return;
        }
        $this->parsings[] = '/\[' . $tag . '\](.+?)\[\/' . $tag . '\]/';
        $this->htmls[]    =
            '<' . $reptag . ' ' . $optionval . "='mailto:\\1'>\\1</"
            . $reptag . '>';
    }

    /**
     * @param string $bbcode
     * @param string $html
     * @return void
     */
    public function cust_tag(string $bbcode = '', string $html = ''): void
    {

        if ($bbcode == '' || $html == '') {
            return;
        }
        $this->parsings[] = $bbcode;
        $this->htmls[]    = $html;
    }

    /**
     * @param string $bbcode
     * @param string $function_name
     * @return void
     */
    public function cust_tag_with_callback(string $bbcode = '', string $function_name = ''): void
    {

        if ($bbcode == '' || $function_name == '') {
            return;
        }
        $this->parsings_with_callbacks[] = $bbcode;
        $this->htmls_with_callbacks[]    = $function_name;
    }

    /**
     * @param $text
     * @return array|string|null
     */
    public function parse_bbcode($text): array|string|null
    {

        $i = 0;
        while (isset($this->parsings[$i])) {

            $text =
                preg_replace($this->parsings[$i], $this->htmls[$i], $text);
            $i++;
        }
        $j = 0;
        while (isset($this->parsings_with_callbacks[$j])) {
            $text = preg_replace_callback($this->parsings_with_callbacks[$j], $this->htmls_with_callbacks[$j], $text);
            ++$j;
        }
        return $text;
    }

    /**
     * @return array
     */
    public function export_parsings(): array
    {
        return $this->parsings;
    }

    /**
     * @return array
     */
    public function export_htmls(): array
    {
        return $this->htmls;
    }
}
