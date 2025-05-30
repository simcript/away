# aWay

## PHP Application Gateway

**aWay** is a lightweight, fast and open-source gateway system developed in PHP. It acts as a central entry point for routing requests to multiple backend services or applications, enabling better control, security, and scalability for your architecture.

## Features

* Lightweight and easy to configure
* Fast and flexible to achieve the goal
* Built entirely with PHP so it runs at minimal cost
* Supports routing to multiple backend services
* Customizable request handling and logging(soon)
* Open-source and community-driven

## Requirements

* PHP 8.2 or higher
* Web server (e.g., Apache, Nginx)

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/simcript/away.git
   ```

2. Configure your routes and services in the `services/` directory.

## Usage

Once installed and configured, all incoming HTTP requests will be routed through the gateway and forwarded to the appropriate backend service.

> Create a directory with service name (similar DEFAULT Service)

> Define a class within the `services/{ServiceName}` directory following the provided example structure, and implement the required methods based on your application's specific needs.

> Define service prefix in index.php

   ```php
   <?php

namespace Services;

use App\Service\ServiceName;

final class Foo extends Service
{

    /**
     * return domain url e.g. https://example.com
     * @return string
     */
    public function getDomain(): string
    {
        return 'https://foo.bar.url';
    }

    /**
     * Returns the corresponding route of a URL
     * e.g. when the corresponding route is an url in target domain
     * or a method defined in domain class
     * '/example/route' => '/sample/route'  // only mapper
     * '/example/route' => 'method'  // run a specific method
     * Note: If a route is not defined,
     * the request is made to the same address on the target domain
     * @param string $url
     * @return string
     */
    public function routeMapper(string $url): string
    {
        return $url;
    }
    
    /**
     * check is allowed header
     * @param string $header
     * @return bool
     */
    public function isAllowedHeader(string $header): bool
    {
        return true;
    }

}
   ```

## Update
Run following command
``git pull``
Or Download ``app`` directory and replace files.

## Contributing

Contributions are welcome! Feel free to open issues, fork the project, and submit pull requests.

## License

This project is licensed under the [MIT License](LICENSE).