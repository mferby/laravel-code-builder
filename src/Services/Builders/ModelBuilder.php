<?php

declare(strict_types=1);

namespace DevLnk\LaravelCodeBuilder\Services\Builders;

use DevLnk\LaravelCodeBuilder\Enums\BuildType;
use DevLnk\LaravelCodeBuilder\Enums\StubValue;
use DevLnk\LaravelCodeBuilder\Exceptions\NotFoundCodePathException;
use DevLnk\LaravelCodeBuilder\Services\StubBuilder;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ModelBuilder extends AbstractBuilder
{
    /**
     * @throws NotFoundCodePathException
     * @throws FileNotFoundException
     */
    public function build(): void
    {
        $modelPath = $this->codePath->path(BuildType::MODEL->value);


        $belongsToStr = $this->codeStructure->hasBelongsTo()
            ? $this->codeStructure->belongsToInModel() : '';

        StubBuilder::make($this->stubFile)
            ->setKey(
                StubValue::USE_SOFT_DELETES->key(),
                StubValue::USE_SOFT_DELETES->value(),
                $this->codeStructure->isSoftDeletes()
            )
            ->setKey(
                StubValue::SOFT_DELETES->key(),
                StubValue::SOFT_DELETES->value().PHP_EOL,
                $this->codeStructure->isSoftDeletes()
            )
            ->setKey(
                StubValue::USE_BELONGS_TO->key(),
                StubValue::USE_BELONGS_TO->value(),
                $this->codeStructure->hasBelongsTo()
            )
            ->setKey(
                StubValue::BELONGS_TO->key(),
                $belongsToStr,
                ! empty($belongsToStr)
            )
            ->setKey(
                StubValue::TIMESTAMPS->key(),
                StubValue::TIMESTAMPS->value().PHP_EOL,
                ! $this->codeStructure->isTimestamps()
            )
            ->setKey(
                StubValue::TABLE->key(),
                StubValue::TABLE->value() . " '{$this->codeStructure->table()}';\n",
                $this->codeStructure->table() !== $this->codeStructure->entity()->str()->plural()->snake()->value()
            )
            ->makeFromStub($modelPath->file(), [
                '{namespace}' => $modelPath->namespace(),
                '{class}' => $this->codeStructure->entity()->ucFirstSingular(),
                '{fillable}' => $this->codeStructure->columnsToModel(),
            ])
        ;
    }
}