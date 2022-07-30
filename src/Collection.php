<?php

namespace Infira\Collection;


class Collection extends \Illuminate\Support\Collection
{
    use extensions\MapOnly;
    use extensions\MapCollect;
    use extensions\MergeOnly;
    use extensions\MapWith;
}