<?php

namespace Tobuli\Importers;

use Exception;
use Symfony\Component\HttpFoundation\File\File;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Importers\Readers\ReaderInterface;

abstract class ImportManager implements ImportMangerInterface
{
    /**
     * @var null|array
     */
    private $fieldsReadMap = null;
    private $validateReaderFormat = true;

    abstract protected function getReadersList(): array;
    abstract public function getImporter(): Importer;

    /**
     * @throws Exception
     */
    public function import($file, array $additionals = [])
    {
        $importer = $this->setValidateReaderFormat(true)->resolve($file);

        $importer->import($file, $additionals);
    }

    public function getImportFields(File $file): array
    {
        $reader = $this->setValidateReaderFormat(false)->selectReader($file);

        return $reader instanceof RemapInterface ? $reader->getHeaders($file) : [];
    }

    /**
     * @throws Exception
     */
    private function resolve($file): Importer
    {
        // select reader only by file format
        $reader = $this->selectReader($file);

        if (is_null($reader)) {
            throw new ValidationException(['id' => trans('front.unsupported_format')]);
        }

        return $this->getImporter()
            ->setReader($reader);
    }

    /**
     * @param $file
     * @return ReaderInterface|null
     */
    private function selectReader($file)
    {
        $readers = $this->getReadersList();

        foreach ($readers as $class) {
            /** @var ReaderInterface $reader */
            $reader = app()->make($class);

            if ($this->fieldsReadMap && $reader instanceof RemapInterface) {
                $reader->setFieldsRenameMap($this->fieldsReadMap);
            }

            if ($this->validateReaderFormat ? !$reader->isValidFormat($file) : !$reader->supportsFile($file)) {
                continue;
            }

            return $reader;
        }

        return null;
    }

    public function setFieldsReadMap($fieldsReadMap): self
    {
        $this->fieldsReadMap = $fieldsReadMap;

        return $this;
    }

    private function setValidateReaderFormat(bool $validateReaderFormat): self
    {
        $this->validateReaderFormat = $validateReaderFormat;

        return $this;
    }
}