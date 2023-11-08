## Sport WebApp and API
It's a ```laravel project``` linked with ```Strava (famous sport app)```.

The main goals of this project are :
- First step : doing a dashboard where I can store my sport activities and display all the data I want.
- Second step : doing an secured API to use these data in my others projects.
- Third step : link this to a mobile app, to easily manage it daily.

### Setup of the project

Clone the project and run the basics command :

``` bash
composer install
npm install
```

Configure your .env with your database.

Add these lines to use the [Strava API](https://developers.strava.com/) :

``` env
CT_STRAVA_CLIENT_ID=
CT_STRAVA_SECRET_ID=
CT_STRAVA_REDIRECT_URI=
```

To get these infos, you must [create your own app on Strava to access their API](https://www.strava.com/settings/api).

When you are ready, launch this :
``` bash
php artisan migrate
npm run dev
```

You can create your user directly in the database or you can uncomment register path in ```routes/auth.php``` to do it via the ```/register``` URL.

Then you can log in to the account and connect your account to Strava (actually the refresh isn't automatic so you need to log in every 6 hours, only if you need to sync an activity).

For the first time, launch the synchro for all activities by accessing the URL ```/strava/sync/all```.

At this step, all should be working !

Now, when you just have a new activity on Strava, just log in again and use the sync button to see it appear on the list.