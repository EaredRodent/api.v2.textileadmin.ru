// node -r esm index

import WebSocket from 'ws'
import fs from 'fs'
import path from 'path'
import { config, log, ClientList, WSMessage } from './classes'

// Чтение deploy.json
const fPath = path.resolve(__dirname, '../web/deploy/deploy.json')
config.deployJson = fs.readFileSync(fPath, 'utf8')

// Запуск сервера
let server = new WebSocket.Server({ port: config.port })
log(`Server is running on port ${config.port}`)
let clientList = new ClientList()

// Подключить к серверу нового пользователя client
server.on('connection', (client, req) => {
  // Добавить client в список подключенных клиентов
  clientList.add(client,req.headers['x-forwarded-for'] || req.connection.remoteAddress)

  // Обработать данные присланные client
  client.on('message', function (data) {
    let wsMessage = WSMessage.fromWSData(data)
    if (wsMessage.type === 'PING') {
      clientList.pong(client)
      Object.assign(client.projectInfo, wsMessage.data || {})
      return
    }

    // Запросы для API, пользователь должен прикрепить token к данным
    if (wsMessage.testToken()) {
      // Мониторинг пользователей
      if (wsMessage.type === 'MONITORING') {
        clientList.returnUserList(client)
      }

      // Рассылка сообщения
      if (wsMessage.type === 'TABLES_UPDATE') {
        clientList.broadcast(wsMessage, client)
      }
    }
  })

  // Обработать отключение client от сервера
  client.on('close', function () {
    clientList.remove(client)
  })
})