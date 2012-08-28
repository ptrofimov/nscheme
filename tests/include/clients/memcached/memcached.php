<?php
/**
 * Эмуляция расширения php_memcached (http://php.net/manual/en/book.memcached.php) *
 * Использовать только для разработки и отладки
 *
 * @package   go-memcached
 * @author    Григорьев Олег aka vasa_c (http://blgo.ru/blog/)
 * @copyright &copy; Григорьев Олег, 2009
 * @version   1.0.1 beta
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL
 * @uses      php_memcache (http://php.net/memcache)
 * @docs      http://blgo.ru/go/memcached/
 */

class Memcached
{

    /**
     **************************************************************
     * Константы, используемые в setOption() и getOption()
     * @link http://www.php.net/manual/en/memcached.constants.php
     * @link http://php.net/manual/en/memcached.setoption.php
     ***************************************************************
     */

    /**
     * OPT_COMPRESSION: включает сжатие данных.
     * При включённом сжатии, значение ключа будет сжато, если превышает определённый объем (сейчас это 100 байт)
     * @var bool по умолчанию TRUE
     */
    const OPT_COMPRESSION = -1001;

    /**
     * OPT_SERIALIZER: указывает алгоритм сериализации нескалярных значений.
     * Значение - одна из констант SERIALIZER_*
     * @var int по умолчанию SERIALIZER_PHP
     */
    const OPT_SERIALIZER      = -1003;

    const SERIALIZER_PHP      = 1; // Стандартная PHP-сериализация
    const SERIALIZER_IGBINARY = 2; // (@link http://opensource.dynamoid.com/) требует расширения IGBINARY

    /**
     * OPT_PREFIX_KEY: задание префикса подставляющегося ко всем ключам.
     * Не длинее 128 символов. Пропорционально снижает максимальный размер ключа.
     * Применяется только к ключам элементов, но не к ключам серверов.
     * @var string по умолчанию отсутствует (пустая строка)
     */
    const OPT_PREFIX_KEY = -1002;

    /**
     * OPT_HASH: определяет алгоритм хэширования ключей элементов
     * Каждый алгоритм имеет свои преимущества и недостатки. Если вы не знаток - оставьте как есть.
     * Значение - одна из констант HASH
     * @var int по умолчанию HASH_DEFAULT
     */
    const OPT_HASH = 2;

    const HASH_DEFAULT  = 0; 
    const HASH_MD5      = 1;
    const HASH_CRC      = 2;
    const HASH_FNV1_64  = 3;
    const HASH_FNV1A_64 = 4;
    const HASH_FNV1_32  = 5;
    const HASH_FNV1A_32 = 6;
    const HASH_HSIEH    = 7;
    const HASH_MURMUR   = 8;

    /**
     * OPT_DISTRIBUTION: метод распределения ключей по серверам (если в пуле больше одного)
     * Алгоритм "consistent hashing" даёт лучшее распределение и позволяет подключать
     * новые сервера с минимальными потерями данных.
     * @var int по умолчанию DISTRIBUTION_MODULA
     */
    const OPT_DISTRIBUTION = 9;

    const DISTRIBUTION_MODULA     = 0; // По модулю
    const DISTRIBUTION_CONSISTENT = 1; // Алгоритм "consistent hashing". 

    /**
     * OPT_LIBKETAMA_COMPATIBLE: устанавливает настройки для libeketama-совместимости.
     * Нужен для совместимости с другими libeketama-клиентами (Python, Ruby ...)
     * Рекомендуется включать эту опцию если вы хотите использовать "consistent hashing"
     * В дальнейшем она может стать включённой по умолчанию.
     * @var bool по умолчанию FALSE
     */
    const OPT_LIBKETAMA_COMPATIBLE = 16;

    /**
     * OPT_BUFFER_WRITER: включение буфферезированного ввода/вывода.
     * @var bool по умолчанию FALSE
     */
    const OPT_BUFFER_WRITES = 10;

    /**
     * OPT_BINARY_PROTOCOL: использование бинарного протокола
     * Нельзя применять к уже открытому подключению
     * @var bool по умолчанию FALSE
     */
    const OPT_BINARY_PROTOCOL = 18;

    /**
     * OPT_NO_BLOCK: включение асинхронного ввода/вывода. Это наиболее быстрый обмен данными.
     * @var bool по умолчанию FALSE
     */
    const OPT_NO_BLOCK = 0;

    /**
     * OPT_CONNECT_TIMEOUT: ожидание подключения к сокету при асинхронном вводе/выводе (мс)
     * @var int по умолчанию 1000 мс.
     */
    const OPT_CONNECT_TIMEOUT = 14;

    /**
     * OPT_RETRY_TIMEOUT: время в секундах перед повторной попыткой подключиться
     * @var int по умолчанию 0 сек.
     */
    const OPT_RETRY_TIMEOUT = 15;

    /**
     * OPT_SEND_TIMEOUT: таймаут записи в сокет для синхронного ввода/вывода
     * @var int по умолчанию 0
     */
    const OPT_SEND_TIMEOUT = 19;

    /**
     * OPT_RECV_TIMEOUT: таймаут чтения сокета для синхронного ввода/вывода
     * @var int по умолчанию 0
     */
    const OPT_RECV_TIMEOUT = 15;

    /**
     * OPT_POLL_TIMEOUT: таймаут подключения в миллисекундах
     * @var int по умолчанию 1000 мс
     */
    const OPT_POLL_TIMEOUT = 8;

    /**
     * OPT_SERVER_FAILURE_LIMIT: через сколько попыток подключения сервер будет удалён из пула
     * @var int по умолчанию 0
     */
    const OPT_SERVER_FAILURE_LIMIT = 21;

    /**
     * OPT_CACHE_LOOKUPS: включение кэшировани DNS
     * @var int по умолчанию 0
     */
    const OPT_CACHE_LOOKUPS = 6;


    /**
     * OPT_TCP_NODELAY: включение подключения к сокету без задержки
     * @var bool по умолчанию FALSE
     */
    const OPT_TCP_NODELAY = 1;

    /**
     * OPT_SOCKET_SEND_SIZE: максимальный буфер сокета на отправку (байты)
     * @var int значение по умолчанию зависит от настроек платформы
     */
    const OPT_SOCKET_SEND_SIZE = 4;

    /**
     * OPT_SOCKET_SEND_SIZE: максимальный буфер сокета на чтение (байты)
     * @var int значение по умолчанию зависит от настроек платформы
     */
    const OPT_SOCKET_RECV_SIZE = 5;

    /**
     **************************************************************
     * Константы описывающие результат действия, возвращаемые getResultCode
     * @link http://www.php.net/manual/en/memcached.constants.php
     * @link http://php.net/manual/en/memcached.getresultcode.php
     ***************************************************************
     */

    const RES_SUCCESS              = 0;  // Операция выполнена успешно
    const RES_FAILURE              = 1;  // Ошибка по ряду причин
    const RES_HOST_LOOKUP_FAILURE  = 2;  // Ошибка поиска по ДНС
    const RES_WRITE_FAILURE        = 5;  // Ошибка при отправке сетевых данных
    const RES_UNKNOWN_READ_FAILURE = 7;  // Ошибка при чтении данных из сети
    const RES_PROTOCOL_ERROR       = 8;  // Ошибочная команда memcached-протокола
    const RES_CLIENT_ERROR         = 9;  // Ошибка на стороне клиента
    const RES_SERVER_ERROR         = 10; // Ошибка на стороне сервера
    const RES_DATA_EXISTS          = 12; // Элемент, который вы пытаетесь сохранить, был изменён
    const RES_NOTSTORED            = 14; // Ключ не сохранён методами "add", "replace" и им подобными
    const RES_NOTFOUND             = 16; // Ключ не найден
    const RES_PARTIAL_READ         = 18; // Ошибка чтения данных из сети
    const RES_SOME_ERRORS          = 19; // Некоторые ошибки при мультизапросе
    const RES_NO_SERVERS           = 20; // Пул серверов пуст
    const RES_END                  = 21; // Конец результата
    const RES_ERRNO                = 25; // Системная ошибка
    const RES_BUFFERED             = 31; // Операция буферезирована
    const RES_TIMEOUT              = 30; // Операция прервана по таймаута
    const RES_BAD_KEY_PROVIDED     = 32; // Плохой ключ
    const RES_PAYLOAD_FAILURE      = -1001; // Невозможно сжать/разжать, сериализоват/десериализовать данные
    const RES_CONNECTION_SOCKET_CREATE_FAILURE = 11; // Не получается создать сетевой сокет


    /**
     **************************************************************
     * Публичные методы класса
     * @link http://www.php.net/manual/en/class.memcached.php
     ***************************************************************
     */

    /**
     * Конструктор. Создание объекта представляющего связь с memcached-сервером
     *
     * @link http://www.php.net/manual/en/memcached.construct.php
     * @param string $persistent_id [optional] все объекты созданные с одним ID будут разделять одно подключение
     */
    public function __construct($persistent_id = '') 
    {
        if ($persistent_id) { // Эмуляция поведения persistent_id
            if (isset(self::$persistentIds[$persistent_id])) {
                $cash = self::$persistentIds[$persistent_id];
                $this->memcache       = $cash->memcache;
                $this->persistentLink = $cash;
            } else {
                $this->memcache = new Memcache();
                self::$persistentIds[$persistent_id] = $this;
            }
        } else {
            $this->memcache = new Memcache();
        }
    }

    /**
     * Добавить сервер в пул
     *
     * Подключение в данный момент не происходит, но если используются опции DISTRIBUTION_CONSISTENT или
     * OPT_LIBKETAMA_COMPATIBLE (см. в списке констант) некоторые внутренние структуры обновляются.
     * Поэтому, если вы добавляете несколько серверов, лучше использовать addServers(), тогда обновление будет одно.
     *
     * Один сервер можно добавлять несколько раз, однако это не желательно, лучше использовать аргумент $weight
     *
     * Если имя сервера неверно, getResultCode() вернёт RES_HOST_LOOKUP_FAILURE
     *
     * Вес сервера определяет вероятность того, что данный сервер будет выбран для операций.
     * Используется с DISTRIBUTION_CONSISTENT и обычно соответствует объёму памяти мемкэша на данном сервере
     *
     * @link http://www.php.net/manual/en/memcached.addserver.php
     * @param string $host   сервер
     * @param int    $port   порт (обычно 11211)
     * @param int    $weight [optional] вес сервера
     * @return bool  успешность операции
     */
    public function addServer($host, $port, $weight = 0)
    {
        $server = Array('host' => $host, 'port' => $port, 'weight' => $weight);
        if ($this->persistentLink) {
            $this->persistentLink->servers[] = $server;
        } else {
            $this->servers[] = $server;
        }
        return $this->memcache->addServer($host, $port);
    }

    /**
     * Добавить список серверов к пулу
     * Для подробностей см. addServer
     *
     * @link http://www.php.net/manual/en/memcached.addservers.php
     * @param array $servers array of array($host, $port [, $weight])
     * @return bool успешность операции
     */
    public function addServers($servers)
    {
        foreach ($servers as $server) {
            $host   = $server[0];
            $port   = $server[1];
            $weight = isset($server[2]) ? $server[2] : 0;
            $this->addServer($host, $port, $weight);
        }
        return true;
    }

    /**
     * Получить список серверов в пуле
     *
     * @link http://www.php.net/manual/en/memcached.getserverlist.php
     * @return array
     */
    public function getServerList()
    {
        return $this->persistentLink ? $this->persistentLink->servers : $this->servers;
    }

    /**
     * Возвращает параметры сервера по ключу
     * Эмулятор всегда выдаёт первый сервер
     *
     * @link http://www.php.net/manual/en/memcached.getserverbykey.php
     * @param string $server_key ключ сервера
     * @return array параметры сервера или FALSE
     */
    public function getServerByKey($server_key)
    {
        $serverList = $this->getServerList();
        if (count($serverList) == 0) {
            return false;
        }
        return $serverList[0];
    }

    /**
     * Сохранить элемент под указанным ключём
     * Можно сохранять значения любых типов PHP, кроме resource
     *
     * @link http://ru.php.net/manual/en/memcached.set.php
     * @param string $key        ключ
     * @param mixed  $value      значение
     * @param int    $expiration [optional] время устаревания в секундах. 0 (по умолчанию) - никогда
     * @return bool  успешность
     */
    public function set($key, $value, $expiration = 0)
    {
        $key = $this->prefix.$key;
        return $this->memcache->set($key, $value, 0, $expiration);
    }

    /**
     * Сохранить значение на определённом сервере
     * В эмуляторе не отличается от set()
     *
     * @link http://ru.php.net/manual/en/memcached.setbykey.php
     * @param string $server_key ключ сервера
     * @param string $key        ключ элемента
     * @param mixed  $value      значение
     * @param int    $expiration [optional] время устаревания
     * @return bool  успешность
     */
    public function setByKey($server_key, $key, $value, $expiration = 0)
    {
        return $this->set($key, $value, $expiration);
    }

    /**
     * Установить несколько значений одним запросом
     *
     * @link http://www.php.net/manual/en/memcached.setmulti.php
     * @param array $items      элементы в формате "key"=>"value"
     * @param int   $expiration [optional] время устаревания для всех
     * @return bool успешность
     */
    public function setMulti($items, $expiration = 0)
    {
        $cash = $this->memcache;
        foreach ($items as $key => $value) {
            $key = $this->prefix.$key;
            $cash->set($key, $value, 0, $expiration);
        }
        return true;
    }

    /**
     * Сохранить набор значений на определённом сервере
     * В эмуляторе не отличается от setMulti()
     *
     * @link http://www.php.net/manual/en/memcached.setmultibykey.php
     * @param string $server_key ключ сервера
     * @param array  $items      элементы в формате "key"=>"value"
     * @param int    $expiration [optional] время устаревания для всех
     * @return bool  успешность
     */
    public function setMultiByKey($server_key, array $items, $expiration = 0)
    {
        $this->setMulti($items, $expiration);
        return true;
    }

    /**
     * Сохранить значение под ещё не существующем ключём
     * Если ключ существует возвращает FALSE, а getResultCode() вернёт RES_NOTSTORED
     *
     * @link http://www.php.net/manual/en/memcached.add.php
     * @param string $key        ключ
     * @param mixed  $value      значение
     * @param int    $expiration [optional] время устаревания
     * @return bool  успешность
     */
    public function add($key, $value, $expiration = 0)
    {
        $key = $this->prefix.$key;
        $r = $this->memcache->add($key, $value, 0, $expiration);
        $this->result = $r ? self::RES_SUCCESS : self::RES_NOTSTORED;
        return $r;
    }

    /**
     * Сохранить значение под ещё не существующем ключён на определённом сервере
     * В эмуляторе не отличается от add()
     *
     * @link http://www.php.net/manual/en/memcached.addbykey.php
     * @param string $server_key ключ сервера
     * @param string $key        ключ элемента
     * @param mixed  $value      значение
     * @param int    $expiration [optional] время устаревания
     * @return bool  успешность
     */
    public function addByKey($server_key, $key, $value, $expiration = 0)
    {
        return $this->add($key, $value, $expiration);
    }

    /**
     * Замена значения под уже существующем ключём
     * Если ключ не существует возвращает FALSE, а getResultCode() вернёт RES_NOTSTORED
     *
     * @link http://www.php.net/manual/en/memcached.replace.php
     * @param string $key        ключ
     * @param mixed  $value      значение
     * @param int    $expiration [optional] время устаревания
     * @return bool  успешность
     */
    public function replace($key, $value, $expiration = 0)
    {
        $key = $this->prefix.$key;
        $r = $this->memcache->replace($key, $value, 0, $expiration);
        $this->result = $r ? self::RES_SUCCESS : self::RES_NOTSTORED;
        return $r;
    }

    /**
     * Замена значения под уже существующем ключём на конкретном сервере
     *
     * http://www.php.net/manual/en/memcached.replacebykey.php
     * @param string $server_key ключ сервера
     * @param string $key        ключ элемента
     * @param mixed  $value      значение
     * @param int    $expiration [optional] время устаревания
     * @return bool  успешность
     */
    public function replaceByKey($server_key, $key, $value, $expiration = 0)
    {
        return $this->replace($key, $value, $expiration);
    }

    /**
     * Удаление элемента по ключу
     * Если ключ не существует возвращает FALSE, а getResultCode() вернёт RES_NOTFOUND
     *
     * Если указать $time=0 или не указывать вообще, ключ будет удалён сразу же
     * Если указать $time, то до этого времени он будет доступен для получения,
     * но не возможно будет его изменить. По достижению заданного времени элемент удалится.
     *
     * @link http://www.php.net/manual/en/memcached.delete.php
     * @param string $key  ключ
     * @param int    $time [optional] время устаревания
     * @return bool успешность
     */
    public function delete($key, $time = 0)
    {
        $key = $this->prefix.$key;
        $r = $this->memcache->delete($key, $time);
        $this->result = $r ? self::RES_SUCCESS : self::RES_NOTFOUND;
        return $r;
    }

    /**
     * Удаление элемента по ключу на конкретном сервере
     * В эмуляторе не отличается от delete
     *
     * @link http://www.php.net/manual/en/memcached.deletebykey.php
     * @param string $server_key ключ сервера
     * @param string $key        ключ элемента
     * @param int    $time       [optional] время устаревания
     * @return bool   успешность
     */
    public function deleteByKey($server_key, $key, $time = 0)
    {
        return $this->delete($key, $time);
    }

    /**
     * Увеличение числового значения
     * Если ключ не существует возвращает FALSE, а getResultCode() вернёт RES_NOTFOUND
     *
     * @link http://www.php.net/manual/en/memcached.increment.php
     * @param string $key    ключ
     * @param int    $offset [optional] на какое число увеличивать
     * @return bool  успешность
     */
    public function increment($key, $offset = 1)
    {
        $key = $this->prefix.$key;
        if ($offset < 0) {
            // В memcache отрицательный инкремент срабатывает, а в memcached возрващает false
            return false;
        }
        $r = $this->memcache->increment($key, $offset);
        $this->result = $r ? self::RES_SUCCESS : self::RES_NOTFOUND;
        return $r;
    }

    /**
     * Уменьшение числового значения
     * Если ключ не существует возвращает FALSE, а getResultCode() вернёт RES_NOTFOUND
     *
     * @link http://www.php.net/manual/en/memcached.decrement.php
     * @param string $key    ключ
     * @param int    $offset [optional] на какое число уменьшать
     * @return bool  успешность
     */
    public function decrement($key, $offset = 1)
    {
        $key = $this->prefix.$key;
        if ($offset < 0) {
            // В memcache отрицательный декремент сбрасывает переменную в 0, в memcached просто возвращает false
            return false;
        }
        $r = $this->memcache->decrement($key, $offset);
        $this->result = $r ? self::RES_SUCCESS : self::RES_NOTFOUND;
        return $r;
    }

    /**
     * Добавление строки в конец уже существующего значения
     * Если ключ не существует возвращает FALSE, а getResultCode() вернёт RES_NOTSTORED
     *
     * Не работает при OPT_COMPRESSION
     *
     * @link http://www.php.net/manual/en/memcached.append.php
     * @param string $key   ключ
     * @param string $value строка для добавления
     * @return bool  успешность
     */
    public function append($key, $value)
    {
        $key = $this->prefix.$key;
        $this->result = self::RES_SUCCESS;
        if ($this->options[self::OPT_COMPRESSION]) {
            return null;
        }
        $v = $this->memcache->get($key);
        if (($v === false) && (!$this->checkExists($key))) {
            $this->result = self::RES_NOTSTORED;
            return false;
        }
        $this->memcache->set($key, $v.$value);
        return true;
    }

    /**
     * Добавление строки в конец уже существующего значения на конкретном сервере
     * В эмуляторе не отличается от append
     *
     * @link http://www.php.net/manual/en/memcached.appendbykey.php
     * @param string $server_key ключ сервера
     * @param string $key        ключ элемента
     * @param string $value      строка для добавления
     * @return bool  успещность
     */
    public function appendByKey($server_key, $key, $value)
    {
        return $this->append($key, $value);
    }

    /**
     * Добавление строки в начало уже существующего значения
     * Если ключ не существует возвращает FALSE, а getResultCode() вернёт RES_NOTSTORED
     *
     * Не работает при OPT_COMPRESSION
     *
     * @link http://www.php.net/manual/en/memcached.prepend.php
     * @param string $key   ключ
     * @param string $value строка для добавления
     * @return bool  успешность
     */
    public function prepend($key, $value, $expiration = 0)
    {
        $key = $this->prefix.$key;
        $this->result = self::RES_SUCCESS;
        if ($this->options[self::OPT_COMPRESSION]) {
            return null;
        }
        $v = $this->memcache->get($key);
        if (($v === false) && (!$this->checkExists($key))) {
            $this->result = self::RES_NOTSTORED;
            return false;
        }
        $this->memcache->set($key, $value.$v);
        return true;
    }

    /**
     * Добавление строки в начало уже существующего значения на конкретном сервере
     * В эмуляторе не отличается от prepend()
     *
     * @link http://www.php.net/manual/en/memcached.prependbykey.php
     * @param string $server_key ключ сервера
     * @param string $key        ключ элемента
     * @param string $value      строка для добавления
     * @return bool  успешность
     */
    public function prependByKey($server_key, $key, $value)
    {
        return $this->prepend($key, $value);
    }

    /**
     * Получение значения элемента
     * Если ключ не существует возвращается FALSE, а getResultCode() вернёт RES_NOTFOUND
     *
     * Про $cache_cb в документации сказано только "Read-through caching callback or NULL".
     * По эксперименту: вызывается в случае если ключ не найден.
     * Получает 3 аргумента: объект memcached, имя переменной и пустую переменную по ссылке
     * Для установки значения для ключа следует записать его в третий аргумент и вернуть TRUE.
     * При этом происходит запись в memcached и get() возвращает записанное значение.
     *
     * @link http://www.php.net/manual/en/memcached.get.php
     * @param string   $key       ключ
     * @param callback $cache_cb  [optional]
     * @param double&  $cas_token [optional] сюда записывается CAS-токен. См. метод cas()
     * @return bool успешность
     */
    public function get($key, $cache_cb = null, &$cas_token = null)
    {
        $keyp = $this->prefix.$key;
        $value = $this->memcache->get($keyp);
        $cas_token = 0;
        if (($value === false) && ((!$this->checkExists($keyp)))) {
            if (!$cache_cb) {
                $this->result = self::RES_NOTFOUND;
                return false;
            }
            /* При включённом allow_call_time_pass_reference в call_user_func нельзя передать значение по ссылке -
              валятся WARINING'и которые перекрываются только @ перед require.
              Поэтому здесь такая порнография с eval()
             */
            $code = 'return call_user_func($cache_cb, $this, $key, &$value);';
            $res  = @eval($code);
            if (!$res) {
                $this->result = self::RES_NOTFOUND;
                return false;
            }
            $this->add($keyp, $value);
        } else {
            $this->result = self::RES_SUCCESS;
        }
        return $value;
    }

    /**
     * Получение значения элемента на конкретном сервере
     * В эмуляторе не отличается от get()
     *
     * @link http://www.php.net/manual/en/memcached.getbykey.php
     * @param string   $server_key ключ сервера
     * @param string   $key        ключ элемента
     * @param callback $cache_cb   [optional] обработчик кэширования
     * @param double&  $cas_token  [optional] сюда записывается CAS-токен. См. метод cas()
     * @return bool успешность
     */
    public function getByKey($server_key, $key, $cache_cb = null, &$cas_token = null) 
    {
        return $this->get($key, $cache_cb, $cas_token);
    }

    /**
     * Получить несколько ключей одним запросом
     *
     * @link http://www.php.net/manual/en/memcached.getmulti.php
     * @param array  $keys       список ключей
     * @param array& $cas_tokens [optional] заполняется CAS-токенами.
     * @return array массив существующих ключей ("ключ" => "значение") или FALSE при ошибке
     */
    public function getMulti(array $keys, &$cas_tokens = null) 
    {
        $result = Array();
        $cas_tokens = Array();
        foreach ($keys as $key) {
            $pkey = $this->prefix.$key;
            $value = $this->memcache->get($pkey);
            if (($value !== false) || ($this->checkExists($pkey))) {
                $result[$key]    = $value;
                $cas_token[$key] = 0;
            }
        }
        return $result;
    }

    /**
     * Получить несколько ключей одним запросом
     * В эмуляторе не отличается от getMulti()
     *
     * @link http://www.php.net/manual/en/memcached.getmultibykey.php
     * @param string $server_key ключ сервера
     * @param array  $keys       список ключей элементов
     * @param array& $cas_tokens [optional] заполняется CAS-токенами. См. cas()
     * @return array массив существующих ключей ("ключ" => "значение") или FALSE при ошибке
     */
    public function getMultiByKey($server_key, $keys, &$cas_tokens = null) 
    {
        return $this->getMulti($keys, $cas_tokens);
    }

    /**
     * Запрос набора значений не ожидая ответа
     * Результаты собираются через методы fetch(), fetchAll() или через callback-функцию в третьем аргументе
     *
     * Из эксперимента:
     * Запись для каждого получаемого значения (только для существующих ключей):
     * Array(
     *     "key"   => ключ,
     *     "value" => значение,
     *     "cas"   => CAS-токен или 0 если $with_cas не указан
     * )
     * Если указана $value_cb, она вызывается для каждого приходящего значения с двумя аргументами:
     * 1. memcached-объект
     * 2. вышеописанный массив
     *
     * @link http://www.php.net/manual/en/memcached.getdelayed.php
     * @param array    $keys     массив запрашиваемых ключей
     * @param bool     $with_cas [optional] если указан, CAS-токены так же запрашиваются. См. cas()
     * @param callback $value_cb [optional] функция обработки приходящих значений
     * @return bool    успешность запроса
     */
    public function getDelayed(array $keys, $with_cas = null, $value_cb = null) 
    {
        $items = $this->getMulti($keys);
        $result = Array();
        foreach ($items as $key => $value) {
             $result[] = Array('key' => $key, 'value' => $value, 'cas' => 0);
        }
        if (!$value_cb) {
            $this->delayed    = $result;
            $this->delayedCur = 0;
            return true;
        }
        foreach ($result as $item) {
            call_user_func($value_cb, $this, $item);
        }
        return true;
    }

    /**
     * Запрос набора значений не ожидая ответа (для конкретного сервера)
     * В эмуляторе не отличается от getDelayed
     *
     * @link http://www.php.net/manual/en/memcached.getdelayedbykey.php
     * @param string   $server_key ключ сервера
     * @param array    $keys       массив запрашиваемых ключей элементов
     * @param bool     $with_cas   [optional] если указан, CAS-токены так же запрашиваются. См. cas()
     * @param callback $value_cb   [optional] функция обработки приходящих значений
     * @return bool    успешность запроса
     */
    public function getDelayedByKey($server_key, $keys, $with_cas = null, $value_cb = null) 
    {
        return $this->getDelayed($keys, $with_cas, $value_cb);
    }

    /**
     * Получение очередного результата из последнего запроса
     * @see getDelayed
     *
     * @link http://www.php.net/manual/en/memcached.fetch.php
     * @return mixed
     */
    public function fetch()
    {
        if (!$this->delayed) {
            $this->result = self::RES_END;
            return false;
        }
        if (!isset($this->delayed[$this->delayedCur])) {
            $this->result = self::RES_END;
            $this->delayed = Array();
            return false;
        }
        $this->result = self::RES_SUCCESS;
        $item = $this->delayed[$this->delayedCur];
        $this->delayedCur++;
        return $item;
    }

    /**
     * Получение всех результатов из последнего запроса
     * @see getDelayed
     *
     * @link http://www.php.net/manual/en/memcached.fetchall.php
     * @return array
     */
    public function fetchAll()
    {
        $d = $this->delayed;
        $this->delayed = Array();
        return $d;
    }

    /**
     * Операция установки значения с проверкой целостности.
     * 
     * CAS-токен - уникальное значение, ассоциированное с элементом. Генерируется мемкэшем.
     * Можно получить из get*() методов.
     * Используется для проверки целостности. При изменении элемента меняется.
     * cas() выполняет set(), проверяя не изменил ли кто-то элемент со времени get().
     *
     * Если со времени выборки элемента данные изменились, то возвращается FALSE,
     * а getResultCode() вернёт RES_DATA_EXISTS.
     *
     * В эмуляторе CAS-токены всегда нулевые
     *
     * @link http://www.php.net/manual/en/memcached.cas.php
     * @param double $token CAS-токен
     * @param string $key   ключ
     * @param mixed  $value значение
     * @param int    $expiration [optional] время устаревания
     * @return bool  успешность
     */
    public function cas($token, $key, $value, $expiration = 0)
    {
        return $this->set($key, $value, $expiration);
    }

    /**
     * Операция установки значения с проверкой целостности на конкретном сервере
     * В эмуляторе не отличается от cas()
     *
     * @link http://www.php.net/manual/en/memcached.casbykey.php
     * @param double $token      CAS-токен
     * @param string $server_key ключ сервера
     * @param string $key        ключ элемента
     * @param mixed  $value      значение
     * @param int    $expiration [optional] время устаревания
     * @return bool успешность
     */
    public function casByKey($token, $server_key, $key, $value, $expiration = 0)
    {
        return $this->cas($token, $key, $value, $expiration);
    }

    /**
     * Получить значение опции memcached 
     * В эмуляторе может работать не всегда корректно
     *
     * @link http://www.php.net/manual/en/memcached.getoption.php
     * @param int $option опция (OPT_*-константа, см. в начале файла)
     * @return mixed значение опции
     */
    public function getOption($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

    /**
     * Установить значение опции memcached
     * В эмуляторе может работать некорректно и не все опции могут давать должный эффект
     *
     * http://www.php.net/manual/en/memcached.setoption.php
     * @param int   $option опция (OPT_*-константа, см. в начале файла)
     * @param mixed $value  значение
     */
    public function setOption($option, $value) 
    {
        $this->options[$option] = $value;
        if ($option == self::OPT_PREFIX_KEY) {
            $this->prefix = $value;
        }
        return true;
    }

    /**
     * Сброс всего кэша
     * Все элементы перестают быть доступны сразу же или по истечении заданного времени
     * (если не были повторно установлены)
     *
     * В эмуляторе не работает $delay
     *
     * @param int $delay [optional] задержка сброса в секундах
     * @return bool успешность
     */
    public function flush($delay = 0) 
    {
        return $this->memcache->flush();
    }

    /**
     * Возвращает состояние серверов
     * См. спецификацию протокола для подробностей
     *
     * В эмуляторе не поддерживается
     *
     * @return array
     */
    public function getStats() 
    {
        return Array();
    }

    /**
     * Получить результат последней операции
     * В эмуляторе может работать не всегда корректно
     *
     * @return int (константа RES_*)
     */
    public function getResultCode() 
    {
        return $this->result;
    }

    /**
     **************************************************************
     * Скрытые методы и поля реализации эмулятора
     ***************************************************************
     */


    /**
     * Проверка существования ключа, если get() возвращает FALSE
     * 
     * @param  string $key ключ
     * @return bool   существует ли он
     */
    private function checkExists($key)
    {
        if ($this->memcache->add($key, 0, 0, 1)) {
            $this->memcache->delete($key);
            return false;
        }
        return true;
    }

    /**
     * Объект php_memcache
     * @var Memcache
     */
    private $memcache;

    /**
     * Результат последней операции
     * @var int (RES_*-константа)
     */
    private $result = self::RES_SUCCESS;

    /**
     * Значение опций
     * @var array
     */
    private $options = Array(
        self::OPT_COMPRESSION          => true,
        self::OPT_SERIALIZER           => self::SERIALIZER_PHP,
        self::OPT_PREFIX_KEY           => '',
        self::OPT_HASH                 => self::HASH_DEFAULT,
        self::OPT_DISTRIBUTION         => self::DISTRIBUTION_MODULA,
        self::OPT_LIBKETAMA_COMPATIBLE => 0,
        self::OPT_BUFFER_WRITES        => 0,
        self::OPT_BINARY_PROTOCOL      => 0,
        self::OPT_NO_BLOCK             => 0,
        self::OPT_TCP_NODELAY          => 0,
        self::OPT_SOCKET_SEND_SIZE     => 0,
        self::OPT_SOCKET_RECV_SIZE     => 0,
        self::OPT_CONNECT_TIMEOUT      => 1000,
        self::OPT_RETRY_TIMEOUT        => 0,
        self::OPT_SEND_TIMEOUT         => 0,
        self::OPT_RECV_TIMEOUT         => 0,
        self::OPT_POLL_TIMEOUT         => 1000,
        self::OPT_CACHE_LOOKUPS        => 0,
        self::OPT_SERVER_FAILURE_LIMIT => 0,
    );

    /**
     * Список серверов
     * @var array
     */
    private $servers = Array();

    /**
     * Префикс ключей
     * @var string
     */
    private $prefix = '';

    /**
     * Результат полученый через getDelayed()
     * @var array
     */
    private $delayed = null;

    /**
     * Курсор в $delayed
     * @var int
     */
    private $delayedCur = 0;

    /**
     * Ссылка на изначальный объект в persistent-списке
     * Нужен для получения списка серверов
     * @var Memcached
     */
    private $persistentLink;

    /**
     * Эмуляция поведения persistent_id
     * @var array of Memcache
     */
    private static $persistentIds = Array();
}

/**
 * Класс, как очевидно, исключений при работе с классом Memcached.
 * Найден в memcached-api.php в исходниках php_memcached.
 * Когда генерируются исключения остаётся загадкой.
 */
class MemcachedException extends Exception
{
    public function __construct($errmsg = '', $errcode = 0) {}
}

?>