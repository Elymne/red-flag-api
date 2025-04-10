<?php

namespace Domain;

interface LocalRedFlagRepository
{
    /**
     * Find as many as possible Redflags depending of args given to the function.
     * If no args are given, we may return nothing.
     * 
     * @param string|null $id
     * @param string|null $firstname
     * @param string|null $surname
     * @param string|null $fullname
     * @param string[]|null $cities - list of cities names.
     * @return RedFlag[]
     */
    function findMany(string|null $id = null, string|null $firstname = null, string|null $surname = null, string|null $fullname = null,  array|null $city = null): array;

    /**
     * Find a unique RedFlag given the id.
     *  
     * @param string $id
     * @return RedFlagDetailed[]
     */
    function findUnique(string $id): RedFlagDetailed;
}
