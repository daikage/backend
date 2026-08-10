# Firebase Cloud Messaging (FCM) Setup Guide

This guide walks you through setting up Push Notifications using Firebase across your Laravel backend and Flutter frontend applications. Since Firebase requires setting up a Google Cloud Project and manipulating secure keys, this cannot be automated without your direct input. 

## 1. Create a Firebase Project
1. Go to the [Firebase Console](https://console.firebase.google.com/).
2. Click **Add Project** and name it `Pairride`.
3. You can disable Google Analytics for now unless you plan to use it.
4. Click **Create Project**.

## 2. Generate Backend Service Account Credentials
Your Laravel backend needs a secure connection to Firebase to dispatch push notifications to devices.

1. In the Firebase Console, go to **Project Settings** (gear icon) > **Service Accounts**.
2. Click **Generate new private key**.
3. A `.json` file will download to your machine. 
4. Move this file to the root of your Laravel backend directory and rename it to `firebase_credentials.json` (do not commit this file to version control!).
5. In your backend `.env`, add:
```env
FIREBASE_CREDENTIALS=firebase_credentials.json
```

*Note: The backend should already have the `kreait/laravel-firebase` package installed (via `composer require kreait/laravel-firebase`). If not, install it and configure `config/firebase.php`.*

## 3. Configure the Flutter Apps (Customer & Driver)
You will need to register both Android apps in Firebase. You'll do this twice: once for `customer_app` and once for `driver_app`.

1. In Project Settings > General, click the **Android icon** to add an Android app.
2. Enter the Android Package Name (e.g., `com.pairride.customer` for the customer app). You can find this in `android/app/build.gradle` under `applicationId`.
3. Register the app, and download the `google-services.json` file.
4. Place the `google-services.json` file inside `customer_app/android/app/`.
5. Repeat steps 1-4 for the driver app (e.g., `com.pairride.driver`).

## 4. Add Firebase Packages to Flutter
In both `customer_app` and `driver_app`, add the core Firebase plugins to your `pubspec.yaml`:

```bash
flutter pub add firebase_core firebase_messaging
```

## 5. Initialize Firebase in `main.dart`
In `main.dart` for both apps, initialize Firebase before `runApp()`:

```dart
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  
  // Request permission
  await FirebaseMessaging.instance.requestPermission();
  
  // Get FCM token
  final fcmToken = await FirebaseMessaging.instance.getToken();
  // TODO: Send this token to your backend via ApiService
  
  runApp(const ProviderScope(child: MyApp()));
}
```

## 6. Update the Database
When the user logs in on the Flutter app, pass their generated FCM token to the backend and store it on their `User` record. Update your Laravel `users` table migration:

```php
$table->string('fcm_token')->nullable();
```

When an event triggers (like `RideRequested` or `RideStatusUpdated`), the backend can now retrieve the `$user->fcm_token` and use the Firebase SDK to send a push notification directly to their device!
