import express from "express";
import cors from "cors";
var app = express();
app.listen(8000);
app.use( express.static("public")  );
app.use( express.json() );
app.use( express.urlencoded( {extended: true}) );
app.use(cors());


var mysql = require("mysql");
var conn = mysql.createConnection({
    user: "publicuser",
    password: "password",
    host: "mysql.danielhsu.dev",
    port: 3306,
    database: "project_hubd"
});

conn.connect(function (err) {
    console.log(err);
})

app.get("/todo/list", function (req, res) {
    conn.query("select * from todoTable", [],
        function (err, rows) {
            res.send( JSON.stringify(rows) );
        }
    )
})

app.get("/todo/item/:id", function (req, res) {
    conn.query("select * from todoTable where todoTableId = ?", 
        [req.params.id],
        function (err, rows) {
            res.send( JSON.stringify(rows[0]) );
        }
    )
})

app.post("/todo/create", function (req, res) {
    conn.query("insert into todoTable (title, isComplete) values (?, ?)", 
        [req.body.title, req.body.isComplete],
        function (err, rows) {
            res.send( JSON.stringify( req.body ));
        }
    )

})

app.put("/todo/item", function (req, res) {
    conn.query("update todoTable set title= ?, isComplete = ? where todoTableId = ?", 
        [req.body.title, req.body.isComplete, req.body.todoTableId],
        function (err, rows) {
            res.send( JSON.stringify( req.body ));
        }
    )

})

app.delete("/todo/delete/:id", function (req, res) {
    conn.query("delete from todoTable where todoTableId = ?",
        [req.params.id], 
        function (err, rows) {
            res.send("#" + req.params.id + " deleted");
        }
    )
})
