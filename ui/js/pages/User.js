import {BasePage} from './Base.js';

import {Map} from '../parts/Map.js?v=2';
import {EmailsGrid} from '../parts/grid/Emails.js?v=2';
import {IpsGrid} from '../parts/grid/Ips.js?v=2';
import {EventsGrid} from '../parts/grid/Events.js?v=2';
import {DevicesGrid} from '../parts/grid/Devices.js?v=2';
import {BaseBarChart} from '../parts/chart/BaseBar.js?v=2';
import {StaticTiles} from '../parts/StaticTiles.js?v=2';
import {UserTiles} from '../parts/details/UserTiles.js?v=2';
import {EventPanel} from '../parts/panel/EventPanel.js?v=2';
import {SingleReviewButton} from '../parts/SingleReviewButton.js?v=2';
import {ScoreDetails} from '../parts/ScoreDetails.js?v=2';
import {PhonesGrid} from '../parts/grid/Phones.js?v=2';
import {IspsGrid} from '../parts/grid/Isps.js?v=2';

import {EmailPanel} from '../parts/panel/EmailPanel.js?v=2';
import {PhonePanel} from '../parts/panel/PhonePanel.js?v=2';
import {DevicePanel} from '../parts/panel/DevicePanel.js?v=2';
import {ReenrichmentButton} from '../parts/ReenrichmentButton.js?v=2';

export class UserPage extends BasePage {

    constructor() {
        super();

        this.initUi();
    }

    initUi() {
        const ACCOUNT_ID = parseInt(window.location.pathname.replace('/id/', ''));

        const getParams = () => {
            return {userId: ACCOUNT_ID};
        };

        const devicesGridParams = {
            url    : '/admin/loadDevices',
            tileId : 'totalDevices',
            tableId: 'devices-table',
            panelType: 'device',
            isSortable: false,

            getParams: getParams
        };

        const eventsGridParams = {
            url    : '/admin/loadEvents',
            tileId : 'totalEvents',
            tableId: 'user-events-table',
            panelType: 'event',

            sessionGroup: true,
            singleUser: true,
            isSortable: false,

            getParams: getParams
        };

        const ipsGridParams = {
            url    : '/admin/loadIps',
            tileId : 'totalIps',
            tableId: 'ips-table',

            isSortable: false,
            orderByLastseen: true,

            getParams: getParams
        };

        const emailsGridParams = {
            url     : '/admin/loadEmails',
            tableId : 'emails-table',
            getParams: getParams,
            panelType: 'email',

            isSortable: false
        };

        const phonesGridParams = {
            url     : '/admin/loadPhones',
            tableId : 'phones-table',
            getParams: getParams,
            panelType: 'phone',

            isSortable: false
        };

        const ispsGridParams = {
            url       : '/admin/loadIsps',
            tableId   : 'isps-table',
            getParams : getParams,
            isSortable: false
        };

        const mapParams = {
            getParams    : getParams,
            tooltipString: 'event',
            tooltipField : 'total_visit'
        };

        const userDetailsTiles = {
            getParams: getParams
        };

        const userScoreDetails = {
            userId:     ACCOUNT_ID
        };

        const chartParams = {
            getParams: function() {
                const id        = ACCOUNT_ID;
                const mode      = 'user';
                const chartType = 'bar';

                return {mode, chartType, id};
            }
        };

        const tilesParams = {
            elems: ['totalCountries', 'totalIps', 'totalDevices', 'totalEvents']
        };

        const staticTiles    = new StaticTiles(tilesParams);
        const detailsTiles   = new UserTiles(userDetailsTiles);
        const reviewButton   = new SingleReviewButton(ACCOUNT_ID);
        const scoreDetails   = new ScoreDetails(userScoreDetails);

        const eventPanel    = new EventPanel();

        const emailPanel    = new EmailPanel();
        const phonePanel    = new PhonePanel();
        const devicePanel   = new DevicePanel();
        const reenrichment  = new ReenrichmentButton();
        const countriesMap  = new Map(mapParams);
        const barChart      = new BaseBarChart(chartParams);

        const ipsGrid               = new IpsGrid(ipsGridParams);
        const ispsGrid              = new IspsGrid(ispsGridParams);
        const emailsGrid            = new EmailsGrid(emailsGridParams);
        const phonesGrid            = new PhonesGrid(phonesGridParams);
        const eventsGrid            = new EventsGrid(eventsGridParams);
        const devicesGrid           = new DevicesGrid(devicesGridParams);
    }
}
