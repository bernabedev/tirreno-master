import {BasePage} from './Base.js';

import {Map} from '../parts/Map.js?v=2';
import {IpsGrid} from '../parts/grid/Ips.js?v=2';
import {UsersGrid} from '../parts/grid/Users.js?v=2';
import {EventsGrid} from '../parts/grid/Events.js?v=2';
import {BaseBarChart} from '../parts/chart/BaseBar.js?v=2';
import {EventPanel} from '../parts/panel/EventPanel.js?v=2';
import {IspTiles} from '../parts/details/IspTiles.js?v=2';

export class IspPage extends BasePage {

    constructor() {
        super();

        this.initUi();
    }

    initUi() {
        //const ISP_ID  = parseInt(window.location.pathname.replace('/isp/', ''));
        const ISP_ID = window.location.pathname.replace('/isp/', '');

        const getParams = () => {
            return {ispId: ISP_ID};
        };

        const usersGridParams = {
            url         : '/admin/loadUsers',
            tileId      : 'totalUsers',
            tableId     : 'users-table',

            isSortable: false,

            getParams: getParams
        };

        const ispDetailsTiles = {
            getParams: getParams
        };

        const eventsGridParams = {
            url    : '/admin/loadEvents',
            tileId : 'totalEvents',
            tableId: 'user-events-table',
            panelType: 'event',

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

        const mapParams = {
            getParams    : getParams,
            tooltipString: 'event',
            tooltipField : 'total_visit'
        };

        const chartParams = {
            getParams: function() {
                const id        = ISP_ID;
                const mode      = 'isp';
                const chartType = 'bar';

                return {mode, chartType, id};
            }
        };

        const countriesMap = new Map(mapParams);
        const eventPanel   = new EventPanel();
        const detailsTiles = new IspTiles(ispDetailsTiles);
        const barChart     = new BaseBarChart(chartParams);

        const ipsGrid      = new IpsGrid(ipsGridParams);
        const usersGrid    = new UsersGrid(usersGridParams);
        const eventsGrid   = new EventsGrid(eventsGridParams);
    }
}
