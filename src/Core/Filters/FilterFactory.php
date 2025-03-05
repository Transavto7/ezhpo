<?php
declare(strict_types=1);

namespace Src\Core\Filters;

abstract class FilterFactory
{
    /** @var string[] */
    protected $filterClasses = [];

    /** @var FilterPipe */
    protected $pipe;

    public function __construct()
    {
        $this->pipe = new DefaultFilterPipe();
    }

    public function createFiltersPipe(array $filters): FilterPipe
    {
        foreach ($filters as $filter => $value) {
            if (isset($this->filterClasses[$filter])) {
                if ($value) {
                    /** @var class-string<Filter> $filterClass */
                    $filterClass = $this->filterClasses[$filter];
                    $filterInstance = $filterClass::create($value);
                    if (! $filterInstance->validateValue()) {
                        throw new \LogicException('Invalid filter type: '.$filter);
                    }
                    $this->pipe->addFilter($filterInstance);
                }
                continue;
            }
            throw new \LogicException('Unknown filter: '.$filter);
        }

        return $this->pipe;
    }

}
