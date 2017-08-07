angular
  .module('myILL', [])
  .component('prmLoansOverviewAfter', {
  bindings: { parentCtrl: '<' },
    controller: function controller($scope, $element, $q, $http, illService, illiadOptions) {
    	var whitelistGroups=illiadOptions.groups;
		  $scope.illBox=false;
  		this.$onInit = function() {
  			/* from: https://github.com/mehmetc/primo-explore-dom/blob/master/js/primo/explore/helper.js */
	  		let rootScope = $scope.$root;
	  		let uSMS=rootScope.$$childHead.$ctrl.userSessionManagerService;
	  		let jwtData = uSMS.jwtUtilService.getDecodedToken();
	  		console.log(jwtData);
	 		  var userGroup=parseInt(jwtData.userGroup);
	  		var user=jwtData.user;
	  		var check = whitelistGroups.indexOf(userGroup);
			  if (check>=0){
				      $scope.illBox=true;
              $scope.showGlobe=true;
              $scope.boxTitle=illiadOptions.boxTitle;
              $scope.illiadURL=illiadOptions.illiadURL;
              console.log($scope.boxTitle);
              var url=illiadOptions.remoteScript;
              var response=illService.getILLiadData(url, user).then(function(response){
                console.log(response);
                $scope.articles=response.data.Articles;
                $scope.requests=response.data.Requests;
                if ($scope.requests || $scope.articles){$scope.showGlobe=false;}
              });
			  }
  		}
    },
  template: `<div class=tiles-grid-tile ng-show={{illBox}}>
              <div class="layout-column tile-content"layout=column>
                <div class="layout-column tile-header"layout=column>
                  <div class="layout-align-space-between-stretch layout-row"layout=row layout-align=space-between>
                    <h2 class="header-link light-text"role=button tabindex=0>
                      <span>{{boxTitle}}</span>
                    </h2>
                  </div>
                </div>
                <md-list class="layout-column md-primoExplore-theme"layout=column role=list>
                </md-list>
                <div class="layout-column layout-align-center-center layout-margin layout-padding message-with-icon"layout=column layout-align="center center"layout-margin=""layout-padding="">
                  <img ng-if="showGlobe" src="custom/LCC/img/globe.png">
                  <div>
                    <p style='font-size: 18px;font-weight: 400;'>Pending Requests</p>
                    <illrequest ng-if="requests" ng-repeat="y in requests" item="y"></illrequest>
                    <div ng-if="!requests">You have no requests.</div>
                      <div style="text-align:center;">----</div>
                    <p style='font-size: 18px;font-weight: 400;''>My Articles</p>
                    <illarticle ng-if="articles" ng-repeat="x in articles" item="x"></illarticle>
                    <div ng-if="!articles">You have no articles.</div>
                    <div style="text-align:center;">----</div>
                    <span>
                      <a href="{{illiadURL}}" target="_blank">Log into your ILL account</a>
                       for more information and to place requests.
                      </span>
                    </div>
                  </div>
                </div>
              </div>`
})
.component('illarticle',{
  bindings: {item: '<'},
  controller: function controller($scope){


console.log(this.item);
//console.log(this.item.index);

    $scope.url=this.item.url;
    $scope.title=this.item.title;
    $scope.item=this.item;
    $scope.jtitle=this.item.jtitle;
    $scope.author=this.item.author;
    $scope.count=this.item.count;
    $scope.expires=this.item.expires;

  },
  template:`<div class='md-list-item-inner' style='padding-bottom:10px;'>
              <div class='md-list-item-text'>
                <p style='font-size: 16px;font-weight: 400;letter-spacing: .01em;margin: 0;line-height: 1.2em;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;'><a href='{{url}}' target='_blank'>{{title}}</a></p>
                <p style='font-size: 14px;letter-spacing: .01em;margin: 3px 0 1px;font-weight: 400;line-height: 1.2em;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;'>{{author}}</p>
                <p style='font-size: 14px;letter-spacing: .01em;margin: 3px 0 1px;font-weight: 400;line-height: 1.2em;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;'>Expires {{expires}}.</p>
              </div>
            </div>`

})
.component('illrequest',{
  bindings: {item: '<'},
  controller: function controller($scope){
    $scope.title=this.item.title;
    $scope.author=this.item.author;
    $scope.count=this.item.count;
  },
  //template:"<p>{{count}}) {{title}}/ {{author}}. </p>"
  template:`<div class='md-list-item-inner' style='padding-bottom:10px;'>
              <div class='md-list-item-text'>
                <p style='font-size: 16px;font-weight: 400;letter-spacing: .01em;margin: 0;line-height: 1.2em;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;'>{{title}}</p>
                <p style='font-size: 14px;letter-spacing: .01em;margin: 3px 0 1px;font-weight: 400;line-height: 1.2em;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;'>{{author}}</p>
              </div>
            </div>`
})
.factory('illService', ['$http',function($http){
  return{
    getILLiadData: function (url, user) {
      return $http({
        method: 'GET',
        url: url,
        params: { 'user': user },
        cache: true
      })
    }
  }
}])
