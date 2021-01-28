/**
 * Created by Luiz Eduardo on 21/05/2017.
 */

var ChatDB = function () {

    var util = require('util');
    var mysql = require('mysql');

    var connection = mysql.createConnection({
        host: 'localhost',
        user: 'sugoi',
        password: 'Sugoi2@21',
        database: 'sugoi_v2'
    });


    //
    // Conecta com o banco de dados
    //
    openConnection = function () {
        connection.connect();
    };

    //
    // PEGANDO DADOS DO USUÁRIO NO BANCO.
    //
    insertMessage = function (conta_id, capitao, canal, message, callback) {

        var sql = util.format('INSERT INTO chat (conta_id, capitao, canal, message) VALUES (\'%s\', \'%s\', \'%s\', \'%s\')', conta_id, capitao, canal, message);

        // var sql = 'INSERT INTO `chat` (`conta_id`, `capitao`, `message`) VALUES (?, ?, ?);';

        connection.query(sql, function (err, rows) {
            if (err) {
                console.error(sql);
            }
            connection.end();
            return callback(err, rows);
        });
    };

    //
    // CLOSE CONNECTION
    //
    closeConnection = function (model, callback) {
        connection.end();
    };

    // Definindo as variaves que serão acessadas
    return {
        insertMessage: insertMessage,
        closeConnection: closeConnection
    }
};

module.exports = ChatDB;
