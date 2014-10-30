App = Ember.Application.create();

App.Router.map(function () {
    this.resource('gnodes');
    this.resource('network', { path: ':network_id' });
});

App.IndexRoute = Ember.Route.extend({

        model: function() {
            return network;
        }

});

var network = [
    {
        id: 'fdgfd-gfdgfdgfd-fdgfd',
        name: 'Test Network',
        players: 0,
        servers: [],
        bungees: [],
        nodes: []
    },
    {
        id: 'xcvbcvxbcxvbcx',
        name: 'Test Network 2',
        players: 0,
        servers: [],
        bungees: [],
        nodes: []
    }
];

