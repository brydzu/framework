Jasny Framework
----

#### PHP framework for Startups

[The lean startup](http://theleanstartup.com/) is the defacto standard for startup nowadays. One of the main principles
is the **build-measure-learn feedback loop**. This principles leads to continues innovation through validated learning,
rather than relying on a grant vision.

> “Lean Startup isn't about being cheap [but is about] being less wasteful and still doing things that are big.”


## Why should you use this framework?

In the early stage of a startup, getting through this feedback loop quickly is essential. At this point the main focus
is getting things working and deployed quickly. Jasny framework helps in speeding up the development process where it
can and staying out of your way otherwise.

As a startups grows, the need higher code quality will arise. Architectural concepts like the [SOLID principles](https://en.wikipedia.org/wiki/SOLID)
are essential when building a large scale maintainable software application. The framework is able to uphold these
principles, though the responsibility for software quality lies with the development team.

Jasny Framework framework is uniquely designed to transition well between these stages.

The framework does not inhibit bad designs and anti-patterns by enforcing strict rules. Design paradigms that are not
understoop or embraced, will not lead to better code quality. At no point should a developer get a feeling like "this
would have already been done long ago if I wasn't using this framework". At that point there is a huge insentive to
bypass these rules.

We accept and expect that the code won't be perfect from day one. Because code improvements are expected, there are
easy to do. This allows your application to enjoy **continuous improvement**, rather than demanding a rebuild from
scratch at any time.

### Service Locator vs Dependency injection

In the documentation you'll often find examples using `App`. This is a service locator that wraps a 
[container](https://github.com/container-interop/fig-standards/blob/master/proposed/container.md). It contains all
global objects like services and factories.

Using a service locator is an easy method to prevent tight coupling and using static methods / sigletons, as those
practices lead to hard-to-test situations. The service locator typically provides just enough decoupling to prevent
this.

Dependency injection is much harder to grasp and apply correctly, but does have real benefits above the service
locator in maintainability and testing. As your application matures you'll likely get to a point where you should move
away from the service locator and exclusively use DI.

Jasny Framework provided a clear path of moving from tight coupling to using a service locator and moving from a
service locator to using dependency injection.


## MVP

A **minimal viable product** is anything that allows you to validate assumptions with minimal effort. An MVP is not an
early version of your product.

> "It is not necessarily the smallest product imaginable...it is simply the fastest way to get through the
> Build-Measure-Learn feedback loop with the minimum amount of effort." 

Most types of MVPs [do not require a developer to build anything](http://blog.strategyzer.com/posts/2015/5/7/dont-build-when-you-build-measure-learn).
Some typical MVPs do require some development and may provide insights that can't or are more difficult to find through
other means.

### Landing page

The default installation of Jasny Framework functions as a landing page.

At this point users can sign up, building a user base and validating the need of your product prior to launch. If
you're not building a product that relies on user sign ups, this can easily be disabled.

It is not the intentation to provide a full features landing page with all marketing features you'll find in solutions
like Unbounce or WordPress. This does allow you to launch your project without hours of starting your project,
creating the release early, release often mindset that is required to come to a short feedback loop.

### Wizard of Oz

A wizard of Oz application provides the end user with a user interface. The application will not have a working
backend and relies a human manually carry out the tasks that would normally be automated.

Submitting an order form might result in an e-mail send to you, rather than being fulfilled on the application: 

```php
class OrderController extends BaseController
{
    public function placOrderAction()
    {
        $data = $this->getInput();
        App::email('order')->with($data)->sendTo('me');
    }
}
```

Jasny Framework is great for this type of MVPs. Setting up a basic web UI is a breeze using Jasny Bootstrap and Twig
templates.

### Prototype

If your solution is technical in nature, building a non-technical MVP can only get you so far. This applies to topics
like artificial intelligence, data encryption and big data processing. In that case building a learning prototype is
required to validate your idea.

PHP is typically not the best fit for these kind of projects and neither is Jasny Framework.


