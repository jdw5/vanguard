<?php

namespace Workbench\App\Tables;

use Jdw5\Vanguard\Table\Table;
use Workbench\App\Enums\TestRole;
use Workbench\App\Models\TestUser;
use Jdw5\Vanguard\Refining\Sorts\Sort;
use Jdw5\Vanguard\Table\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Jdw5\Vanguard\Refining\Filters\Filter;
use Jdw5\Vanguard\Refining\Options\Option;
use Jdw5\Vanguard\Table\Actions\BulkAction;
use Jdw5\Vanguard\Table\Actions\PageAction;
use Jdw5\Vanguard\Table\Actions\InlineAction;
use Jdw5\Vanguard\Refining\Filters\QueryFilter;
use Jdw5\Vanguard\Refining\Filters\SelectFilter;

class PreferenceTable extends Table
{
    protected $defaultPerPage = 10;
    protected $showKey = 'show';

    protected function definePagination()
    {
        return [
            5,
            10, 
            20,
            50
        ];
    }

    protected function definePreferenceKey()
    {
        return 'prefs';
    }

    protected function defineQuery()
    {
        return TestUser::query()
            ->select('id', 'name', 'email', 'created_at', 'updated_at', 'role');
    }

    protected function defineColumns(): array
    {
        return [
            Column::make('id')->asKey()->hide(),
            Column::make('name')->preference()->sort(),
            Column::make('email')->preference(true)->fallback('No email')->sort(),
            Column::make('created_at')->preference(true)->transform(fn ($value) => $value->format('d/m/Y H:i:s')),
            Column::make('updated_at')->preference()->transform(fn ($value) => $value->format('d/m/Y H:i:s')),
            Column::make('role')->label('User Role')
        ];
    }

    protected function defineRefinements(): array
    {
        return [
            Filter::make('name')->loose(),
            SelectFilter::make('type')->options(Option::enum(TestRole::class, 'label')),
            QueryFilter::make('id')->query(fn (Builder $builder, $value) => $builder->where('id', '<', $value)),
           
            Sort::make('created_at', 'newest')->desc()->default(),
            Sort::make('created_at', 'oldest')->asc(),   
        ];
    }

    protected function defineActions(): array
    {
        return [
            PageAction::make('add')->label('Add User'),

            BulkAction::make('delete')->label('Delete Users'),

            InlineAction::make('view')->label('View User')->default(),
            InlineAction::make('edit')->label('Edit User'),
            InlineAction::make('delete')->label('Delete User')

        ];
    }
}