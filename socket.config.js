module.exports = {
    apps: [{
        script: "socket.js",
        cwd: "socket",
        watch: true,
        // Delay between restart
        watch_delay: 1000,
        ignore_watch : ["node_modules"],
        watch_options: {
            "followSymlinks": false
        }
    }]
}