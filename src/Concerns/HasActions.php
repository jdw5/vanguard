<?php

namespace Jdw5\SurgeVanguard\Concerns;

use Jdw5\SurgeVanguard\Table\Actions\BaseAction;
use Jdw5\SurgeVanguard\Table\Actions\BulkAction;
use Jdw5\SurgeVanguard\Table\Actions\PageAction;
use Jdw5\SurgeVanguard\Table\Actions\InlineAction;
use Illuminate\Support\Collection;

trait HasActions
{
    protected mixed $cachedActions = null;

    public function getActions(bool $showHidden = false): Collection
    {
        return $this->cachedActions ??= collect($this->defineActions())
            ->when(!$showHidden)
            ->filter(static fn (BaseAction $action): bool => !$action->isHidden());
    }

    public function getInlineActions(bool $showHidden = false): Collection
    {
        return $this->getActions($showHidden)->filter(static fn (BaseAction $action): bool => $action instanceof InlineAction);
    }

    public function getBulkActions(bool $showHidden = false): Collection
    {
        return $this->getActions($showHidden)->filter(static fn (BaseAction $action): bool => $action instanceof BulkAction);
    }

    public function getPageActions(bool $showHidden = false): Collection
    {
        return $this->getActions($showHidden)->filter(static fn (BaseAction $action): bool => $action instanceof PageAction);
    }

    public function getDefaultAction(): ?BaseAction
    {
        return $this->getActions()->first(static fn (BaseAction $action): bool => $action instanceof InlineAction && $action->isDefault());
    }

    protected function defineActions(): array
    {
        return [];
    }
}