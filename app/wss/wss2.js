const os = require('os')

///

const prodHostName = 'fvds-2'

///

let hostName = os.hostname()

const MOD_ENV = (hostName === prodHostName) ? 'prod' : 'dev'
const MOD_PROD = (MOD_ENV === 'prod')
const MOD_DEV = (MOD_ENV === 'dev')


console.log(MOD_DEV)


