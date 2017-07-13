<?php
/**
 * Created by PhpStorm.
 * User: voggenre
 * Date: 28.04.2017
 * Time: 07:48
 */

namespace AppBundle\Question;


abstract class QuestionStatus
{
    const Unanswered = 'unanswered';
    const Answered   = 'answered';
    const Deleted    = 'deleted';
    const All        = 'all';
}