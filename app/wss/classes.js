/**
 * Конфиг сервера
 * @type {{secret_key: string, port: number}}
 */
const config = {
  port: 6001,
  token: '149509e79053e4e2af391c01ab56fb6d646f6b434b1a3350532c4065061e3748',
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
   */
  add (client) {
    this.clients.push(client)
    this.resetPingTimeOut(client)
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

        // todo
        if(wsMessage.type === 'RELOAD') {
          client.send(JSON.stringify(['ALL_CONTACTS_RELOAD_PAGE']))
        }
      }
    })
    log(`Broadcast message: "${data}"`)
  }

  /**
   * Ответить PONG сообщением клиенту
   * @param client
   */
  pong(client) {
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

  // Сверяет секретный ключ с секретным ключем в config
  testToken() {
    let token = this.token
    delete this.token
    return token === config.token
  }
}

module.exports = {
  config,
  log,
  ClientList,
  WSMessage
}
