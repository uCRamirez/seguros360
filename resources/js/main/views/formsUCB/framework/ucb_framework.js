import $ from 'jquery';

var RESTurl = 'http://' + document.location.host + '/api/v1/';
var token = localStorage.getItem("auth_token");


const ucb_framework = () => {
    // Parameters: 
    // sqlquery => A SQL query like INSERT, UPDATE, DELETE or SELECT
    // callback => A function to execute after a good request
    //
    // This function execute only SQL query's
    const UCB_executeQuery = async (sqlquery, callback) => {
        // Audit the action
        UCB_audit(sqlquery);
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: RESTurl + 'execute-query',
                contentType: "application/json",
                dataType: "json",
                headers: {
                    "Authorization": token ? "Bearer " + token : "",
                },
                data: JSON.stringify({ query: sqlquery }),
                success: function (resp) {
                    if (callback != null) callback(resp);
                        resolve(resp);
                    },
                error: function (e) {
                    console.error("Error: ", e);
                reject(e);
                }
            });
        });
    };

    // Parameters:
    // actionAudit => The action taken by auditing
    //
    // This function audit action
    const UCB_audit = async (actionAudit) => {
        // The final action to save
        actionAudit = `{"action":{"Execute the following event:":"${actionAudit}"}}`;
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: RESTurl + 'audit',
                contentType: "application/json",
                dataType: "json",
                headers: {
                    "Authorization": token ? "Bearer " + token : "",
                },
                data: JSON.stringify({ action: actionAudit }),
                success: function (resp) {    
                },
                error: function (e) {
                    console.error("Error: ", e);
                reject(e);
                }
            });
        });
    };

    // Parameters:
    // file => The csv file to upload the informacion 
    // table => Table where you want to upload the information
    // sentence => The necessary modifications (refer to the syntax of MSQL LOAD IN FILE).
    // callback => A function to execute after a good request
    //
    // To upload a csv file
    const UCB_uploadFile = (file,table,sentence,callback) => {
        // Audit the action
        UCB_audit(`uploaded the following file: ${file.name}`);
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: RESTurl + 'execute-query',
                contentType: "application/json",
                dataType: "json",
                headers: {
                    "Authorization": token ? "Bearer " + token : "",
                },
                data: JSON.stringify({ query: sqlquery }),
                success: function (resp) {
                    if (callback != null) callback(resp);
                        resolve(resp);
                    },
                error: function (e) {
                    console.error("Error: ", e);
                reject(e);
                }
            });
        });
    };



    
    // Return functions to use in the crm
    return {
        UCB_executeQuery,
        UCB_audit,
        UCB_uploadFile
    };
  };
  
  export default ucb_framework;
