<?php

namespace Infira\Collection;


class Collection extends \Illuminate\Support\Collection
{
    use extensions\MapOnly;
    use extensions\MapIntoCollection;
    use extensions\MergeOnly;
}