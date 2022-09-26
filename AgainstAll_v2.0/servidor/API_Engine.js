const express = require('express')

const app = express();
const fs = require('fs')

const puerto = 3000;

app.use(function (req, res, next) { // permite el acceso desde cualquier URL
    res.setHeader('Access-Control-Allow-Origin', '*');
next();});
app.set('trust proxy', true); // devuelve la ip real al acceder a la propiedad req.ip

app.get("/",(req, res) => { // comprobacion de actividad
    res.send('hola');
});

app.get("/mapa",(req, res) => { // devolver mapa del juego
    fs.readFile( 'mapa.txt' , 'utf8' , function ( error , datos ) {
        if(error) return console.log(error);
        res.send( datos );
    });
});

app.get("/player",(req, res) => { // devolver la lista de jugadores
    fs.readFile( 'player.txt' , 'utf8' , function ( error , datos ) {
        if(error) return console.log(error);
        res.send( datos );
    });
});

app.get("/city",(req, res) => { // devolver las ciudades elegidas
    fs.readFile( 'ciudadesElegidas.txt' , 'utf8' , function ( error , datos ) {
        if(error) return console.log(error);
        res.send( datos );
		console.log(`Lista de ciudades enviada a ${req.ip}`);
    });
});

app.listen(puerto, '0.0.0.0', () => { // a la escucha
    console.log(`A la escucha en el puerto ${puerto}`);
});