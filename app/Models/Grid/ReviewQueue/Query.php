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

namespace Models\Grid\ReviewQueue;

class Query extends \Models\Grid\Base\Query {
    protected $defaultOrder = null;
    protected $dateRangeField = 'event_account.lastseen';

    public function getData(): array {
        $queryParams = $this->getQueryParams();

        $queryParams[':reviewed'] = false;
        $queryParams[':low_score'] = \Utils\Constants::USER_LOW_SCORE_SUP;

        $query = (
            'SELECT
                event_account.id        AS accountid,
                event_account.userid    AS accounttitle,
                event_account.created   AS created,
                event_account.is_important,
                event_account.score_updated_at,
                event_account.score,
                event_account.firstname,
                event_account.lastname,
                event_account.lastseen,

                event_email.email

            FROM
                event_account

            LEFT JOIN event_email
            ON (event_account.lastemail = event_email.id)

            WHERE
                event_account.key = :api_key
                AND (
                    event_account.reviewed = :reviewed
                    OR event_account.fraud IS NULL
                )
                AND event_account.score <= :low_score
                %s'
        );

        $this->applySearch($query, $queryParams);
        $this->applyOrder($query);
        $this->applyLimit($query, $queryParams);

        return [$query, $queryParams];
    }

    public function getTotal(): array {
        $queryParams = $this->getQueryParams();

        $queryParams[':reviewed'] = false;
        $queryParams[':low_score'] = \Utils\Constants::USER_LOW_SCORE_SUP;

        $query = (
            'SELECT
                COUNT (event_account.id)

            FROM
                event_account

            LEFT JOIN event_email
            ON (event_account.lastemail = event_email.id)

            WHERE
                event_account.key = :api_key
                AND (
                    event_account.reviewed = :reviewed
                    OR event_account.fraud IS NULL
                )
                AND event_account.score <= :low_score
                %s'
        );

        $this->applySearch($query, $queryParams);

        return [$query, $queryParams];
    }

    public function getTotalOverall(): array {
        $queryParams = $this->getQueryParams();

        $queryParams[':reviewed'] = false;
        $queryParams[':low_score'] = \Utils\Constants::USER_LOW_SCORE_SUP;

        $query = (
            'SELECT
                COUNT(event_account.id) AS count

            FROM
                event_account

            WHERE
                event_account.key = :api_key
                AND (
                    event_account.reviewed = :reviewed
                    OR event_account.fraud IS NULL
                )
                AND event_account.score <= :low_score'
        );

        return [$query, $queryParams];
    }

    private function applySearch(string &$query, array &$queryParams): void {
        $this->applyDateRange($query, $queryParams);

        $searchConditions = '';
        $search = $this->f3->get('REQUEST.search');

        if (is_array($search) && isset($search['value']) && is_string($search['value']) && $search['value'] !== '') {
            $searchConditions .= (
                " AND
                (
                    LOWER(REPLACE(
                            COALESCE(event_account.firstname, '') ||
                            COALESCE(event_account.lastname, '') ||
                            COALESCE(event_account.firstname, ''),
                            ' ', '')) LIKE LOWER(REPLACE(:search_value, ' ', '')) OR
                    LOWER(event_email.email)       LIKE LOWER(:search_value) OR

                    TO_CHAR(event_account.lastseen::timestamp without time zone, 'dd/mm/yyyy hh24:mi:ss') LIKE :search_value OR
                    TO_CHAR(event_account.created::timestamp without time zone, 'dd/mm/yyyy hh24:mi:ss') LIKE :search_value
                )"
            );

            $queryParams[':search_value'] = '%' . $search['value'] . '%';
        }

        //Add search and ids into request
        $query = sprintf($query, $searchConditions);
    }
}
