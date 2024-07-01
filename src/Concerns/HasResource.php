<?php

namespace Conquest\Table\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

trait HasResource
{
    protected $resource = null;

    protected function setResource($resource): void
    {
        if (is_null($resource)) {
            return;
        }

        if (is_string($resource)) {
            // If it's a string, assume it's a class name and store it
            if (class_exists($resource)) {
                $this->resource = $resource;
            } else {
                throw new \InvalidArgumentException("Class {$resource} does not exist.");
            }
        } else {
            $this->resource = $resource;
        }
    }

    public function getResource()
    {
        if (isset($this->resource)) {
            if (is_string($this->resource)) {
                // If it's a string (class name), create a query
                return $this->resource::query();
            }
            if ($this->resource instanceof \Illuminate\Database\Eloquent\Model) {
                return $this->resource->newQuery();
            }

            return $this->isBuilderInstance() ? $this->resource : $this->resource->query();
        }

        // Check if the resource() function is defined
        if (method_exists($this, 'resource')) {
            return $this->resource();
        }

        // Else, try to resolve a model from name
        $modelClass = str(static::class)
            ->classBasename()
            ->beforeLast('Table')
            ->singular()
            ->prepend('\\App\\Models\\')
            ->toString();

        if (class_exists($modelClass)) {
            return $modelClass::query();
        }

        throw new \RuntimeException('Unable to resolve resource for '.static::class);
    }

    public function getBaseModel()
    {
        $resource = $this->getResource();

        if ($resource instanceof Builder) {
            return $resource->getModel();
        }

        if ($resource instanceof \Illuminate\Database\Eloquent\Model) {
            return $resource;
        }

        if ($resource instanceof QueryBuilder) {
            return DB::table($resource->from);
        }

        throw new \RuntimeException('Unable to get base model for resource');
    }

    public function isBuilderInstance()
    {
        $resource = $this->getResource();

        return $resource instanceof Builder || $resource instanceof QueryBuilder;
    }
}