<?php

namespace App\Services;

/**
 * Splits a long text into small pieces (chunks) so that each piece
 * can be searched and sent to the AI separately.
 */
class Chunker
{
    /**
     * @return string[]
     */
    public function split(string $text, int $maxChars = 800): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", trim($text));
        $paragraphs = preg_split('/\n{2,}/', $text) ?: [];

        $chunks  = [];
        $current = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            // A single very long paragraph: cut it by length
            if (mb_strlen($paragraph) > $maxChars) {
                if ($current !== '') {
                    $chunks[] = $current;
                    $current  = '';
                }

                foreach (mb_str_split($paragraph, $maxChars) as $piece) {
                    $piece = trim($piece);
                    if ($piece !== '') {
                        $chunks[] = $piece;
                    }
                }

                continue;
            }

            // Start a new chunk if adding this paragraph makes it too big
            if ($current !== '' && mb_strlen($current) + mb_strlen($paragraph) + 2 > $maxChars) {
                $chunks[] = $current;
                $current  = $paragraph;
            } else {
                $current = $current === '' ? $paragraph : $current."\n\n".$paragraph;
            }
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }
}
