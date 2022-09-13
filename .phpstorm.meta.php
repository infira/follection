<?php

namespace PHPSTORM_META {

    override(
        \Infira\Follection\Handlers\Record::get(),
        map([
            '' => \Infira\Follection\Handlers\Field::class
        ])
    );
    override(
        \Infira\Follection\Handlers\Record::__get(),
        map([
            '' => \Infira\Follection\Handlers\Field::class
        ])
    );
    override(
        \Infira\Follection\Handlers\Record::offsetGet(),
        map([
            '' => \Infira\Follection\Handlers\Field::class
        ])
    );
    //override(\Infira\Follection\Storage\CollectionGateway::getPipedInto(1), type(1));
}