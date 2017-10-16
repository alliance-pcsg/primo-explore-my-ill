# primo-explore-my-ill
Load illiad requests &amp; articles into Primo "My Account"

## Features
Creates a pane in Primo New UI "My Account". Queries ILLiad for user's current requests and received articles, and displays them in the pane, including direct links to articles.

### Screenshot
![screenshot](screenshot.png)

## Install
1. Make sure you've installed and configured [primo-explore-devenv](https://github.com/ExLibrisGroup/primo-explore-devenv).
2. Navigate to your template/central package root directory. For example:
    ```
    cd primo-explore/custom/MY_VIEW_ID
    ```
3. If you do not already have a `package.json` file in this directory, create one:
    ```
    npm init -y
    ```
4. Install this package:
    ```
    npm install primo-explore-my-ill --save-dev
    ```
5. Generate an ILLiad Web Platform API Key, as described [here](https://prometheus.atlas-sys.com/display/illiad/The+ILLiad+Web+Platform+API).

6. Deploy the php file to a server accessible to you. Edit lines 7, 10, and 11.

7. Place the .htaccess file in the same directory as your PHP file to add CORS authorization.

## Usage
Once this package is installed, add `my-ill` as a dependency for your custom module definition.

```js
var app = angular.module('viewCustom', ['myILL'])
```

Note: If you're using the `--browserify` build option, you will need to first import the module with:

```javascript
import 'primo-explore-oadoi-link';
```

You can configure the banner by passing a configuration object. All properties are required.

| name      | type         | usage                                                                                   |
|-----------|--------------|-----------------------------------------------------------------------------------------|
| `groups` | array       | array of alma user group codes in which this should appear                                               |
| `remoteScript` | string       | url of server-side php script                                               |
| `boxTitle` | string       | Text to appear at the top of the ILL box                                               |
| `illiadURL` | string       | url of your ILLiad login page                                               |
| `apiURL` | string       | url of ILLiad Web Platform endpoint for Transactions/UserRequests (documentation [here](https://prometheus.atlas-sys.com/display/illiad/The+ILLiad+Web+Platform+API))                                               |

The code below adds a banner similar to the above image.

```js
app.constant('illiadOptions', {
  "groups": [0,1,2,3],
  "remoteScript": "https://mydomain.com/illiad.php",
  "boxTitle": "My Library Interlibrary Loan",
  "illiadURL": "https://illiad.myinstitution.edu/illiad/Logon.html",
  "apiURL": "https://illiad.myinstitution.edu/ILLiadWebPlatform/Transaction/UserRequests/",


})
```

<!-- ## Running tests
1. Clone the repo
2. Run `npm install`
3. Run `npm test` -->
