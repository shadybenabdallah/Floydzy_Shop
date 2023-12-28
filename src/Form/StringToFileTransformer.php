<?php

namespace App\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToFileTransformer implements DataTransformerInterface
{
    private $photosDirectory;

    public function __construct(string $photosDirectory)
    {
        $this->photosDirectory = $photosDirectory;
    }

    public function transform($value): ?File
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return new File($this->photosDirectory.'/'.$value);
    }

    public function reverseTransform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof File) {
            throw new TransformationFailedException('Expected a File object.');
        }

        return $value->getFilename();
    }
}
