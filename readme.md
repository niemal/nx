## nx analytics

**This project is in its really early phase, a lot of things will/might change.**

The main goal of this project is to provide open-source back-end analytics along with optional front-end javascript code which would have to reach the very same code that can be included for back-end type of analytics. Currently the main language used is PHP along with MySQL, although those things might be extended/ported/binded in the future.

Upon installation the user will have to choose between 2 modes:
	- simple
		- Log URL, user agent and referer. Use those elements to produce modular statistics. Very light.
	- advanced
		- will log and track IP addresses (including those behind proxies), parse user agents, and log referers. Overall better for data manipulation along with detailed and highly customizible statistics for the user's flavor.

The way we see this at the moment the 2 modes are not compatible with each other.

An admin page will be used, one the user can log into and review statistics.
