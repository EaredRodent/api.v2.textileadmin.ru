<?php
/**
 *  * Created by PhpStorm.
 * User: x3RABBITx3
 * Date: 03.06.2019
 * Time: 18:25
 */

namespace app\modules\v1\models;


use app\gii\GiiSlsOrder;

class SlsOrder extends GiiSlsOrder
{
    const s0_del          = 's0_del';
    const s0_preorder     = 's0_preorder';
    const s1_prep         = 's1_prep';
    const s1_wait_assembl = 's1_wait_assembl';
    const s5_assembl      = 's5_assembl';
    const s2_wait         = 's2_wait';
    const s3_accept       = 's3_accept';
    const s4_reject       = 's4_reject';
    const s6_allow        = 's6_allow';
    const s7_send         = 's7_send';
}