

USE `stlmodernmopar`;


INSERT INTO `user_accounts` (
	`username`, `password`, `salt`, `state`, `city`, `creationDate`, `creationIP`, `lastActive`, `lastIP`, `mod`, `staff`
) VALUES (
	'Test', '23dfe2a1cbd55672467c355de3caaa3ead9f2dc7c7e3d77128e525981765a37bbcbd51be5e7360d3c690e0d3e8077499ef310fd7efd585bb390736356f1f5209', '#1<.[e0QdS.*7B=jVs9Kpn96=55828nK23r4#X0,r#M78{Y(2Q#Qki^4J08!P;4W,-<=!m%N5/1e]33p]9,28[A7M=:bs6@>:o{X5Ql96Z>[0m.XR5!gJ3z0<&9s/z[s', 
	'IL', 'OFallon', '2015-01-01 00:00:00', '0.0.0.0', '2015-01-01 00:00:00', '0.0.0.0', 1, 1
), (
	'Test2', '23dfe2a1cbd55672467c355de3caaa3ead9f2dc7c7e3d77128e525981765a37bbcbd51be5e7360d3c690e0d3e8077499ef310fd7efd585bb390736356f1f5209', '#1<.[e0QdS.*7B=jVs9Kpn96=55828nK23r4#X0,r#M78{Y(2Q#Qki^4J08!P;4W,-<=!m%N5/1e]33p]9,28[A7M=:bs6@>:o{X5Ql96Z>[0m.XR5!gJ3z0<&9s/z[s', 
	'IL', 'OFallon', '2015-01-01 00:00:00', '0.0.0.0', '2015-01-01 00:00:00', '0.0.0.0', 1, 0
), (
	'Test3', '23dfe2a1cbd55672467c355de3caaa3ead9f2dc7c7e3d77128e525981765a37bbcbd51be5e7360d3c690e0d3e8077499ef310fd7efd585bb390736356f1f5209', '#1<.[e0QdS.*7B=jVs9Kpn96=55828nK23r4#X0,r#M78{Y(2Q#Qki^4J08!P;4W,-<=!m%N5/1e]33p]9,28[A7M=:bs6@>:o{X5Ql96Z>[0m.XR5!gJ3z0<&9s/z[s', 
	'IL', 'OFallon', '2015-01-01 00:00:00', '0.0.0.0', '2015-01-01 00:00:00', '0.0.0.0', 0, 0
);