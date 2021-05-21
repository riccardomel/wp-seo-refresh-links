# wp-seo-refresh-links

Version: 1.0.3

Note: Require Wordpress > 5.0 (or minor versions using Gutemberg Plugin).

Easily schedule new publish date for your posts! Useful for seo or marketings speed ups!
Plan to revive your old wordpress posts!
Scan every 30min default. (you can customize it)

## Installation User

1. Install it on your Wordpress Site!
2. Edit or add post, open the new sidebar inside Gutemberg Editor
3. Activate the re-publish (Yes,No) and set the future date
4. Enjoy!

## Installation Devs

1. Clone the repo and install:

```npm
npm install  
```

3. Build and watch code:

```npm
npm run dev
```

4. Production Mode:

```npm
npm run build
```

5. Uploads (without node_modules folder) in your wp-contents/plugin/wp-seo-refresh-links folder.

6. Activate it

7. Open Gutemberg

![img](https://riccardomel.com/github/screenshots/wp-seo-refresh-link.png)

## How-to tutorial (it)

https://www.targetweb.it/plugin-wordpress-per-programmare-la-ripubblicazione/

## Changelog

1.0.0 - Release  
1.0.1 - Fix Scheduler on FeedRss  
1.0.2 - Fix Iframe and embed inside post after re-publish  
1.0.3 - Fix 403 error when save with some other plugin (such as Amp for Wp)  
1.0.4 - Fix critical error (month) formatting in new version of Gutemberg

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[GNU](https://choosealicense.com/licenses/agpl-3.0/)