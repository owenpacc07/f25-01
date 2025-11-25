// calls php file which manages flag file
// type: 0 = read value of flag file, 1 = reset flag file to 0
// Wrapper of fetch api
// Returns true if flagFileValue is 1, false if 0.
//core-c
const api_url = SITE_VERSION + "/api";

export async function fetchPHP(type, mid) {
   /* const response = await fetch(`${httpcore_c}/manage-flag-file.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        // enter mid and flag file action 
        body: JSON.stringify({
            'type': type,
            'mechanism': mid,
        })
    })
    const flagFileValue = await response.text(response);

    console.log("Flag file value is: " + flagFileValue)
    return flagFileValue && true || false;  */ 
    return true;
}

export async function fetchIO(user_id, experiment_id, family_id, mechanism_id) {
    console.log('fetchIO params:', { user_id, experiment_id, family_id, mechanism_id });
    const response = await fetch(api_url + "/get-io-experiment.php", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        body: `user_id=${encodeURIComponent(user_id)}&experiment_id=${encodeURIComponent(experiment_id)}&family_id=${encodeURIComponent(family_id)}&mechanism_id=${encodeURIComponent(mechanism_id)}`
    });
    const data = await response.json();
    console.log('fetchIO response:', data);
    return data;
}