<?php

/**
 * Tirreno ~ Open source user analytics
 * Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Tirreno Technologies Sàrl (https://www.tirreno.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.tirreno.com Tirreno(tm)
 */

namespace Views;

abstract class Base {
    public $f3;
    public $data = [];

    public function __construct() {
        $f3 = \Base::instance();
        $this->f3 = $f3;
    }

    public function render(): string|false|null {
        if (!is_array($this->data)) {
            $this->data = [];
        }

        return null;
    }
}
