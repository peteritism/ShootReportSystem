To Do List

General
- [ ] Top nav bar
- [ ] Breadcrumbs for easy navigation and clear positioning within the site
- [ ] Security
	- [ ] trim whitespace
	- [ ] htmlspecialchars
	- [ ] limit default user abilities
- [ ] Create site users and permissions
	-use shooter ID's as users?
	
scoreEditor
- [ ] stop user from leaving page without saving scores
	- [ ] indicate changes scores via CSS

eventShooterEditor
- [ ] score check: look for empty values in scores
- [ ] add total number of shooter
- [ ] add phone number to shooter
- [ ] add email to shooter
- [ ] ability to edit shooters
- [ ] ability to remove event shooters
- [ ] ability to edit event shooters
- [ ] ability to add existing shooters
	idea:type in nsca number, if found put data into form for editing, else new form  How to find peole without nsca numbers?
- [ ] make first, last, state, class required (new shooters don't have nsca numbers yet)
- [x] add lady to concurrent field &LY

scoreReport
- [ ] add points system for concurrents
	- [ ] properly calculate points for multiple concurrencies (i.e., lady + concurrent)
- [ ] add option winners (no money, only indicate if won/percentage take)
- [ ] add hidden data (not really private per se)
	- [ ] add official score report
	- [ ] add money calculations
		- [ ] calculate NSCA target fees
		- [ ] calculate income
		- [ ] calculate money for option winners
			- [ ] generate list of names/address for money winners
				-possibly indicated paid status (though this would be more complicated and require all sorts of database reorganization to be done properly and it would still be prone to problems)
		- [ ] compile winnings list with address and whatnot
- [ ] add event statistics

- [ ] add some JS to get tables the same size

shootReport
- [ ] add shootReport

clubReport
- [ ] add clubReport

- [ ] caching system
	- indicate change in data with a toggle on the event (if changed: changed = 1)
		-look for changed status, on first page generation, cache page set changed = 0