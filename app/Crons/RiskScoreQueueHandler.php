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

namespace Crons;

class RiskScoreQueueHandler extends AbstractQueueCron {
    private \Models\Rules $rulesModel;

    public function __construct() {
        parent::__construct();

        $actionType = new \Type\QueueAccountOperationActionType(\Type\QueueAccountOperationActionType::CalulcateRiskScore);
        $this->accountOperationQueueModel = new \Models\Queue\AccountOperationQueue($actionType);
        $this->rulesModel = new \Models\Rules();
    }

    public function processQueue(): void {
        if ($this->accountOperationQueueModel->isExecuting() && !$this->accountOperationQueueModel->unclog()) {
            $this->log('Risk score queue is already being executed by another cron job.');
        } else {
            $this->processItems($this->accountOperationQueueModel);
        }
    }

    protected function processItem(array $item): void {
        $rulesController = new \Controllers\Admin\Rules\Data();
        $rulesController->updateScoreByAccountId($item['event_account'], $item['key'], $this->rulesModel);
    }
}
