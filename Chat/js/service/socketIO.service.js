/**
 * Created by Luiz Eduardo on 24/06/2017.
 */

(function () {
    'use strict';

    angular.module('sugoi.chat').factory('SocketIO',
        function () {

            var socket;

            var getSocket = function () {
                if (!socket) {
                    var url = window.location.href;
                    socket = io.connect('http://35.208.245.100:8080');
                }
                return socket;
            };


            return {
                getSocket: getSocket
            };
        });
}());