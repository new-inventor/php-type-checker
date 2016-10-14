#PHP type checker

Проверяет совпадение типа переменной с указанными типами. Может бросить исключение если необходимо.

##Установка

через composer

`composer require new-inventor/php-type-checker`

##Принцип работы

Подключаем трейт к классу

`use TypeCheck;`

После этого в классе появляется статический метод `param(int $paramIndex = 0)`

Теперь можно проверять типы.
Простые типы:
array
bool
callable
double
float
int
integer
long
null
numeric
object
real
resource
scalar
string

Для проверки простых типов необходимо сделать следующее:

`self::param(1)->tint->tstring->fail()`
или
`self::param(0)->tint->tstring->result()`

метод `fail()` предназначен для бросания исключения
метод `result()` предназначен для возвращения результата

если надо проверить элементы параметра-массива то необходимо вызвать метод `inner()` и после него определять типы

`self::param()->tstring->tarray->tint->inner()->tint->tstring->result()`
проверка внутренних элементов будет происходить, только если параметр является массивом.

Для проверки типов можно вызвать метод `types()` в параметрах которого перечислить имена типов

`self::param()->types(MyClass::class, MyAnotherClass::class)`

если нужна более сложная проверка то используйте метод `callback(callable $callback)`

```
self::param()->int->float->callback(function ($value){
    return $value > 10 && $value < 100;
});
```
