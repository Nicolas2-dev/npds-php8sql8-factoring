<?php

use Modules\TwoPollbooth\Support\Polls;


if (! function_exists('PollNewest'))
{
    /**
     * [PollNewest description]
     *
     * @param   int  $id  [$id description]
     *
     * @return  [type]    [return description]
     */
    function PollNewest(?int $id = null)
    {
        return Polls::PollNewest($id);
    }
}
