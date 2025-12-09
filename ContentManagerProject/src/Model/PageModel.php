<?php

namespace App\Model;

class PageModel
{
    public int $id;
    public string $slug;
    public string $title;
    public string $content;

    public static function fromArray(array $data): self
    {
        $page = new self();

        foreach ($data as $key => $value) {
            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($page, $setter)) {
                $page->$setter($value);
                continue;
            }

            if (property_exists($page, $key)) {
                $page->$key = $value;
            }
        }

        return $page;
    }
}