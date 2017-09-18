#PHP type checker

Проверяет совпадение типа переменной с указанными типами. Может бросить исключение если необходимо.

##Установка

через composer

`composer require new-inventor/php-type-checker`

##Принцип работы

Вызываем статический метод `TypeChecker::check($value)`

Теперь можно проверять типы.

Простые типы:
* array
* bool
* callable
* float
* int
* null
* numeric
* object
* resource
* scalar
* string

Для проверки простых типов необходимо сделать следующее:

`TypeChecker::check($value)->tint()->tstring()->fail()`
или
`TypeChecker::check($value)->tint()->tstring()->result()`

* метод `fail()` предназначен для бросания исключения(`TPMailSender\TypeChecker\Exception\TypeException`)
* метод `result()` предназначен для возвращения результата проверки

Если надо проверить элементы параметра-массива то необходимо вызвать метод `inner()` и после него определять типы.
Можно не вызывать функцию `tarray()` перед вызовом метода `inner()`

`TypeChecker::check($value)->tstring()->tarray()->tint()->inner()->tint()->tstring()->result()`
проверка внутренних элементов будет происходить, только если параметр является массивом.

Для проверки типов объектов вызвается метод `types()` в параметрах которого перечисляются полные имена типов

`TypeChecker::check($value)->types(MyClass::class, MyAnotherClass::class)`

если нужна более сложная проверка то используйте метод `callback(callable $callback)`

```
self::param()->tint()->tfloat()->tstring()->callback(function ($value){
    return is_object($value) && method_exists($value, '__toString');
});
```
