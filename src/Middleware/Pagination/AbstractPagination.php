<?php

namespace App\Integration\View\ViewMiddleware\Pagination;

use Countable, Iterator, ArrayAccess;
use Traversable;
use UnexpectedValueException;

abstract class AbstractPagination implements Countable, Iterator, ArrayAccess
{
    protected int $currentPageNumber = 1;
    protected int $numItemsPerPage;
    protected array $items = array();
    protected int $totalCount = 0;
    protected array $paginatorOptions = [];
    protected array $customParameters = [];

    public function rewind(): void {
        reset($this->items);
    }

    public function current(): mixed {
        return current($this->items);
    }

    public function key(): string|int|null {
        return key($this->items);
    }

    public function next(): void {
        next($this->items);
    }

    public function valid(): bool {
        return key($this->items) !== null;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function setCustomParameters(array $parameters): void
    {
        $this->customParameters = $parameters;
    }

    public function getCustomParameter(string $name): mixed
    {
        return $this->customParameters[$name] ?? null;
    }

    public function setCurrentPageNumber($pageNumber): void
    {
        $this->currentPageNumber = $pageNumber;
    }

    /**
     * Get currently used page number
     *
     * @return integer
     */
    public function getCurrentPageNumber(): int {
        return $this->currentPageNumber;
    }

    public function setItemNumberPerPage($numItemsPerPage): void
    {
        $this->numItemsPerPage = $numItemsPerPage;
    }

    /**
     * Get number of items per page
     *
     * @return integer
     */
    public function getItemNumberPerPage(): int {
        return $this->numItemsPerPage;
    }

    public function setTotalItemCount($numTotal): void
    {
        $this->totalCount = $numTotal;
    }

    /**
     * Get total item number available
     *
     * @return integer
     */
    public function getTotalItemCount(): int {
        return $this->totalCount;
    }

    public function setPaginatorOptions($options): void
    {
        $this->paginatorOptions = (array)$options;
    }

    /**
     * Get pagination alias
     *
     * @param string $name
     * @return ?string
     */
    public function getPaginatorOption(string $name): ?string {
        return $this->paginatorOptions[$name] ?? null;
    }

    public function setItems($items): void
    {
        if (!is_array($items) && !$items instanceof Traversable) {
            throw new UnexpectedValueException("Items must be an array type");
        }
        $this->items = $items;
    }

    /**
     * Get current items
     *
     * @return array
     */
    public function getItems(): array {
        return $this->items;
    }

    public function offsetExists($offset): bool {
        return array_key_exists($offset, $this->items);
    }

    public function offsetGet($offset): mixed
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (null === $offset) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
}
