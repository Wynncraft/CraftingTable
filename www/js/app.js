App = Ember.Application.create();

App.Router.map(function () {
    this.resource('gnodes');
    this.resource('login');
    this.resource('network', { path: '/network/:network_id' });
});

App.NetworksController = Ember.ObjectController.extend({
    networks: function() {
        return networks;
    }.property("[]")
});

var networks = [
    {
        id: 'fdgfd-gfdgfdgfd-fdgfd',
        name: 'Test Network',
        players: 0,
        servers: [],
        bungees: [],
        nodes: []
    },
    {
        id: 'xcvb-cvxbcxv-bcx',
        name: 'Test Network 2',
        players: 0,
        servers: [],
        bungees: [],
        nodes: []
    }
];

