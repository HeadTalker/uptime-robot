<h1>Uptime Robot One Pager</h1>

<h3>All Your Monitors In 1 Place</h3>

![Alt text](http://i.imgur.com/m98uKyX.png)

<h3>View Graphs Of Response Times</h3>

![Alt text](https://i.imgur.com/04YeVfe.png)

<h3>Installation:</h3>

1.  Clone the repository
  ```
  git clone git@github.com:HeadTalker/uptime-robot.git
  ```

2.  Copy config-sample.php name it config.php

  ```
  cd uptime-robot && cp config-sample.php config.php
  ```

3.  Edit config.php and put an Uptime Robot API key ( must be account specific )

  ```
  $UP_ACCOUNT_API_KEY = "replace_with_yours";
  ```
4.  Launch the app

```
# This may vary depending where you installed the app and/or your environment
http://localhost/uptime-robot/main/
```

<h3>Building/Making Edits</h3>

We use <a href="https://www.npmjs.com/">NPM</a>, <a href="http://bower.io/">Bower</a>, and <a href="http://gruntjs.com/">Grunt</a>.  We recommend you use these tools to make changes to the app.

<h5>Node Modules Install:</h5>

```
npm install
```

<h5>Bower Components Install:</h5>

```
bower install
```

<h5>Running Grunt Tasks:</h5>

```
grunt
```

<h3>Bugs and issues</h3>

You can report them <a href="https://github.com/HeadTalker/uptime-robot/issues">here</a>

<h3>Contributing</h3>

Anyone is welcome to contribute.   You can contribute in a few ways.

1.  Submitting a pull request with fixes, improvements, or new features
2.  Reporting bugs or issues <a href="https://github.com/HeadTalker/uptime-robot/issues">here</a>
3.  Providing feedback or suggestions
