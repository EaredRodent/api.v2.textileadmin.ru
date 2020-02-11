/**
 * Конфиг сервера
 * @type {{secret_key: string, port: number}}
 */
const config = {
  port: 6001,
  broadcastingToken: '149509e79053e4e2af391c01ab56fb6d646f6b434b1a3350532c4065061e3748',  // Для массовой рассылки с API
  monitoringToken: '0bdbc7d7fb6a47c5d6199046f40ed226b75e087dc18b7ba8928361afb677e974', // Для мониторинга пользователей
  deployJson: ''
}

/**
 * Лог
 * @param message
 */
function log (message) {
  console.log(`[${new Date().toLocaleTimeString()}] ${message}`)
}

class ClientList {
  /**
   * Создать список для подключенных клиентов
   */
  constructor () {
    this.clients = []
    log('ClientList created')
  }

  /**
   * Добавить клиента в список
   * @param client
   * @param ip
   */
  add (client, ip) {
    this.clients.push(client)
    this.resetPingTimeOut(client)
    client.projectInfo = {
      tsConnected: Date.now(),
      ip
    }
    log(`New client connection! Clients: ${this.clients.length}`)
  }

  /**
   * Удалить клиента из списка
   * @param client
   */
  remove (client) {
    this.clearPingTimeOut(client)

    let removeIndex = this.clients.indexOf(client)
    if (removeIndex !== -1) {
      this.clients.splice(removeIndex, 1)
      log(`Close client connection. ${removeIndex}. Clients: ${this.clients.length}`)
    }
  }

  /**
   * Разослать сообщение всем клиентам из списка, игнорируя sender
   * @param { WSMessage } wsMessage
   * @param sender - пользователь WS, который отправил сообщение, игнорируется при рассылке
   */
  broadcast (wsMessage, sender = {}) {
    let data = wsMessage.toString()

    this.clients.forEach(client => {
      if ((client !== sender) && (client.readyState === 1)) {
        client.send(data)
      }
    })
    log(`Broadcast message: "${data}"`)
  }

  /**
   * Ответить PONG сообщением клиенту
   * @param client
   */
  pong (client) {
    if (client.readyState === 1) {
      this.resetPingTimeOut(client)

      let wsPong = new WSMessage('PONG', config.deployJson)
      client.send(wsPong.toString())
    }
  }

  /**
   * Сбросить таймаут на ожидание PING сообщения от клиента
   * @param client
   */
  resetPingTimeOut (client) {
    this.clearPingTimeOut(client)

    client.pingTimeOut = setTimeout(() => {
      log('Client do not PING me :c')
      client.terminate()  // Вызывает событие client.on('close'...
    }, 10000)
  }

  /**
   * Удалить таймаут на ожидание PING сообщения от клиента
   * @param client
   */
  clearPingTimeOut (client) {
    if (client.pingTimeOut) {
      clearTimeout(client.pingTimeOut)
    }
  }

  /**
   * Возвращает информацию о пользователях WS
   * @param client
   */
  returnUserList (client) {
    let userList = this.clients

    userList = userList.map(client => client.projectInfo)
    userList.forEach(projectInfo => projectInfo.tsOnline = Date.now() - projectInfo.tsConnected)
    userList = userList.filter(projectInfo => projectInfo.login)

    let message = new WSMessage('USER_LIST', userList)

    if (client.readyState === 1) {
      client.send(message.toString())
    }
  }
}

/**
 * Объект сообщения
 * Поля: type, data, token. - Могут быть равны undefined
 */
class WSMessage {
  // Создать из существующих данных
  constructor (type, data, token) {
    this.type = type
    this.data = data
    this.token = token
  }

  // Создать из on message data
  static fromWSData (wsData) {
    let wsMessage = new WSMessage()
    try {
      let eventData = JSON.parse(wsData)
      wsMessage.type = eventData.type
      wsMessage.data = eventData.data
      wsMessage.token = eventData.token
    } catch (e) {}
    return wsMessage
  }

  // Вернуть строковое представление
  toString () {
    return JSON.stringify(this)
  }

  // Сверяет секретный ключ API с секретным ключем в config
  testToken () {
    let token = this.token
    delete this.token
    return token === config.broadcastingToken
  }
}

module.exports = {
  config,
  log,
  ClientList,
  WSMessage
}
