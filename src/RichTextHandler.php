<?php

namespace Sunqianhu\Helper;

class RichTextHandler
{
    /**
     * 转保持格式的纯文本
     * @param $richText
     * @return string
     */
    public function toMaintainFormatPlainText($richText)
    {
        $richText = preg_replace('#<(script|style)\b[^>]*\s*>.*?</\s*\1>#si', '', $richText);
        $richText = preg_replace('/<!--.*?-->/s', '', $richText);

        $blockTags = [
            'div', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'li', 'tr', 'th', 'td', 'blockquote', 'pre', 'section'
        ];
        foreach ($blockTags as $tag) {
            $richText = preg_replace('#<' . $tag . '\b[^>]*?>#i', "", $richText);
            $richText = preg_replace('#</' . $tag . '\b[^>]*?>#i', "\n", $richText);
        }
        $richText = preg_replace('#<br\s*/?>#i', "\n", $richText);

        $richText = strip_tags($richText);
        $richText = preg_replace("/[ \t]+/", ' ', $richText);
        $richText = html_entity_decode($richText, ENT_QUOTES, 'UTF-8');

        return trim($richText);
    }

    /**
     * 纯文本转富文本（HTML）
     * @param string $plainText
     * @return string
     */
    public function toRichText($plainText)
    {
        $paragraphs = preg_split("/\n\s*\n/", $plainText);

        $html = '';
        foreach ($paragraphs as $paragraph) {
            $paragraph = str_replace("\n", '<br/>', $paragraph);
            $html .= "<p>{$paragraph}</p>";
        }

        return $html;
    }
}